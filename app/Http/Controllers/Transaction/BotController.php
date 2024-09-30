<?php

namespace App\Http\Controllers\Transaction;

use App\Events\RoomChatBroadcast;
use App\Http\Controllers\Controller;
use App\Repositories\Master\Answer\AnswerRepositoryInterface;
use App\Repositories\Master\BotService\BotServiceRepositoryInterface;
use App\Repositories\Master\Contact\ContactRepositoryInterface;
use App\Repositories\Master\Question\QuestionRepositoryInterface;
use App\Repositories\Transaction\Bot\BotRepositoryInterface;
use App\Repositories\Transaction\ChatRoom\ChatRoomRepositoryInterface;
use App\Repositories\Transaction\ChatRoomDetail\ChatRoomDetailRepositoryInterface;
use App\Services\BotService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    public $view, $route;
    protected $repository, $botService, $contactRepository, $chatRoomRepository, $chatRoomDetailRepository, $botServiceRepository, $questionRepository, $answerRepository;
    public function __construct(
        BotRepositoryInterface $repository,
        BotService $botService,
        ContactRepositoryInterface $contactRepository,
        ChatRoomRepositoryInterface $chatRoomRepository,
        ChatRoomDetailRepositoryInterface $chatRoomDetailRepository,
        BotServiceRepositoryInterface $botServiceRepository,
        QuestionRepositoryInterface $questionRepository,
        AnswerRepositoryInterface $answerRepository
    ) {
        $this->repository = $repository;
        $this->botService = $botService;
        $this->contactRepository = $contactRepository;
        $this->chatRoomRepository = $chatRoomRepository;
        $this->chatRoomDetailRepository = $chatRoomDetailRepository;
        $this->botServiceRepository = $botServiceRepository;
        $this->questionRepository = $questionRepository;
        $this->answerRepository = $answerRepository;
        $this->view = 'transaction.bot';
        $this->route = 'bot';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }

    public function create()
    {
        $tenants = [];
        return view("{$this->view}.create", compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => 'required',
            'session_name' => 'required|unique:bots,session_name'
        ]);
        $getToken = $this->botService->generateToken($data['session_name']);
        if ($getToken['status'] >= 200 && $getToken['status'] < 300) {
            $data['token'] = $getToken['data']['token'];
            try {
                DB::transaction(function () use ($request, $data) {
                    $this->repository->create($data);
                });
                alertNotif('save');
            } catch (Exception $e) {
                Log::error($e->getMessage());
                alertNotif('error', $e->getMessage());
            }
        } else {
            $message = $getToken['message'];
            if (!is_null($getToken['data']) && count($getToken['data']) > 0) {
                if (isset($getToken['data']['message'])) {
                    $message .= ' | ' . $getToken['data']['message'];
                }
            }
            Log::error($message);
            alertNotif('error', $message);
        }
        return redirect()->route("{$this->route}.index");
    }

    public function edit($id)
    {
        try {
            $data = $this->repository->find($id);
            return view("{$this->view}.edit", compact('data'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $repository = $this->repository->find($id);
        $data = $request->validate([
            //
        ]);
        $data['updated_by'] = auth()->user()->id;
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $this->repository->update($id, $data);
            });
            alertNotif('update');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $repository->delete();
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function startSession($id)
    {
        $botService = $this->botService;
        $model = $this->repository->find($id);
        $botService->startSession($model);

        return redirect()->route("{$this->route}.index");
    }
    public function closeSession($id)
    {
        $botService = $this->botService;
        $model = $this->repository->find($id);
        $botService->closeSession($model);

        return redirect()->route("{$this->route}.index");
    }
    public function logoutSession($id)
    {
        $botService = $this->botService;
        $model = $this->repository->find($id);
        $botService->logoutSession($model);

        return redirect()->route("{$this->route}.index");
    }

    public function chat(Request $request, BotService $botService)
    {
        if (isset($request->event)) {
            if ($request->event == 'onmessage') {
                $bot = $this->repository->getData([], [], [], null, [
                    [
                        'session_name',
                        '=',
                        $request->session
                    ]
                ], 'first');

                $phone_number = explode("@", $request->from);
                if ($phone_number[1] !== 'g.us') {
                    $phone_number = $phone_number[0];

                    // check is number valid
                    if ($this->botService->checkContact($bot, $phone_number)) {
                        $contact_data = $this->contactRepository->contactFromBot($bot, $phone_number);
                        $this->botService->setTyping($bot, ['isTyping' => true, 'phone_number' => $phone_number]);

                        $chat_room_data = [
                            'contact_id' => $contact_data->id,
                            'unread_count' => is_null($contact_data->chatRoom) ? 1 : $contact_data->chatRoom->unread_count + 1
                        ];
                        $chat_room = (is_null($contact_data->chatRoom)) ? $this->chatRoomRepository->create($chat_room_data) : $this->chatRoomRepository->update($contact_data->chatRoom->id, $chat_room_data);
                        $chat_room_detail = $this->chatRoomDetailRepository->storeMessage($chat_room, $request->toArray());
                        if ($chat_room->status == '0') {
                            switch ($request->type) {
                                case 'chat':
                                    Log::info('1');
                                    $checkAutoreply = $this->questionRepository->getData([], [], [], null, [['question_text', '', $request->content]], 'first');
                                    Log::info('2');
                                    if (is_null($checkAutoreply)) {
                                        $bot_services = $this->botServiceRepository->getData([], [], [], null, [['bot_id', '=', $bot->id]], 'all');

                                        $botService->generateListMessage($bot, $contact_data, $bot_services);
                                    }
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
            }
            $this->botService->setTyping($bot, ['isTyping' => false, 'phone_number' => $phone_number]);

        }
        // Log::info($request->all());
        // if (isset($request->event)) {
        //     if ($request->event == 'onmessage') {
        //         $from = $request->from;
        //         $from = explode("@c.us", $from);
        //         $from = $from[0];
        //         $setTyping = [
        //             'phone_number' => $from,
        //             'isTyping' => true
        //         ];
        //         $to = $request->to;
        //         $bot = Bot::where('phone_number', $to)->first();
        //         $botService->setTyping($bot, $setTyping);
        //         $contact = Contact::whereRaw("CONCAT(country_code, contact_number) = ?", [$from])->first();
        //         if (is_null($contact)) {
        //             $jsonUrl = 'https://gist.githubusercontent.com/anubhavshrimal/75f6183458db8c453306f93521e93d37/raw/f77e7598a8503f1f70528ae1cbf9f66755698a16/CountryCodes.json';
        //             $jsonResponse = Http::get($jsonUrl);

        //             // Mendekodekan respons JSON menjadi array
        //             $countryCodes = $jsonResponse->json();
        //             $country_code = '';
        //             foreach ($countryCodes as $countryCode) {
        //                 $dialCode = ltrim($countryCode['dial_code'], '+');
        //                 if (strpos($from, $dialCode) === 0) {
        //                     $country_code = $dialCode;
        //                 }
        //             }
        //         }
        //         switch ($request->type) {
        //             case 'chat':
        //                 $text = $request->content;
        //                 $checkAutoreply = AutoReply::where('reference_message', $text)->first();
        //                 if (is_null($checkAutoreply)) {
        //                     $getAutoReply = AutoReply::all();
        //                     $data['description'] = "Halo $contact->name, Kamu menghubungi layanan customer service Kreasi Sawala Nusantara.\nKamu ingin terhubung dengan siapa nih?";
        //                     $data['buttonText'] = "Pilih Layanan";
        //                     $botService->generateListMessage($bot, $contact, $data, $getAutoReply, true);
        //                     $setTyping['isTyping'] = false;
        //                     $botService->setTyping($bot, $setTyping);
        //                 }
        //                 break;
        //             case 'list_response':
        //                 $response_id = $request->listResponse['singleSelectReply']['selectedRowId'];
        //                 $response_text = $request->listResponse['title'];
        //                 $response_id = explode('-', $response_id);
        //                 $response_header = $response_id[0];
        //                 $response_detail = $response_id[1];
        //                 if ($response_detail == 0) {
        //                     /* First Response */
        //                     $getAutoReply = AutoReplyDetail::where('auto_reply_id', $response_header)->whereNull('previous_reply_id')->get();
        //                     if (is_null($getAutoReply)) {
        //                         return response()->json([], 200, []);
        //                     }
        //                     $data['description'] = "Halo $contact->name, Kamu menghubungi layanan customer service " . $getAutoReply[0]->autoReplyHeader->name . ".";
        //                     $data['buttonText'] = "Pilih Layanan " . $getAutoReply[0]->autoReplyHeader->name;

        //                 } else {
        //                     $getAutoReply = AutoReplyDetail::where('auto_reply_id', $response_header)->where('previous_reply_id', $response_detail)->get();
        //                     if (is_null($getAutoReply)) {
        //                         return response()->json([], 200, []);
        //                     }
        //                     $data['description'] = "Apa yang ingin kamu tau selanjutnya dari " . $response_text . "?";
        //                     $data['buttonText'] = "Pilih Layanan " . $response_text;
        //                 }
        //                 $botService->generateListMessage($bot, $contact, $data, $getAutoReply);
        //                 break;
        //             default:
        //                 Log::info($request->all());
        //                 break;
        //         }
        //     }
        // }
        // $setTyping['isTyping'] = false;
        // $botService->setTyping($bot, $setTyping);
        // return response()->json([], 200, []);
    }

    public function syncChat(Request $request)
    {
        try {
            $bot = $this->repository->find(1);
            $syncBot = $this->botService->syncChat($bot);
            alertNotif('success', "Sync Processing started on background");
            return redirect()->back();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->back();
        }

    }

}
