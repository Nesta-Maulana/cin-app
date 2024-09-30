<?php
namespace App\Services;

use App\Jobs\SyncChatJob;
use App\Models\Bot;
use App\Repositories\Master\Contact\ContactRepositoryInterface;
use App\Repositories\Transaction\ChatRoom\ChatRoomRepositoryInterface;
use App\Repositories\Transaction\ChatRoomDetail\ChatRoomDetailRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Log;

class BotService
{
    protected $contactRepository, $chatRoomRepository, $chatRoomDetailRepository;
    public function handle($contact, $message)
    {
        $response = Http::post(env('APP_BOT_URL') . '/api/chat', [
            'chatId' => $contact->country_code . $contact->contact_number,
            'message' => $message,
        ]);
        return json_decode($response);
    }
    public function generateToken($session_name)
    {
        try {
            $response = Http::post(env('APP_BOT_URL') . '/' . $session_name . '/' . env('SECRET_KEY_BOT') . '/generate-token', []);
            $result = [
                'status' => $response->status(),
                'data' => $response->json(),
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => $response->status(),
                'data' => $response->json(),
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        }
    }

    public function startSession($bot)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bot->token
            ])->post(env('APP_BOT_URL') . '/' . $bot->session_name . '/start-session', []);
            $data = $response->json();
            if (is_null($data['status'])) {
                if (is_null($bot->phone_number)) {
                    $this->startSession($bot);
                } else {
                    $checkSessionStatus = $this->getSessionStatus($bot->session_name, $bot->token);
                    if ($checkSessionStatus['status'] >= 200 && $checkSessionStatus['status'] < 300) {
                        if ($checkSessionStatus['data']['status'] == 'CLOSED') {
                            $this->startSession($bot);
                        }
                    } else {
                        $this->startSession($bot);
                    }
                }
            }
            $bot->status = 'connected';
            $bot->save();
            $result = [
                'status' => $response->status(),
                'data' => $data,
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => $response->status(),
                'data' => $response->json(),
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        }
    }
    public function closeSession($bot)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bot->token
            ])->post(env('APP_BOT_URL') . '/' . $bot->session_name . '/close-session', [
                        'webook' => env('APP_CENTRAL_DOMAIN') . '/api/chat',
                        'waitQrCode' => true,
                    ]);
            $data = $response->json();
            if (is_null($data['status']) || !$data['status']) {
                $this->closeSession($bot);
            }
            $bot->status = 'disconnected';
            $bot->save();
            $result = [
                'status' => $response->status(),
                'data' => $data,
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => $response->status(),
                'data' => $response->json(),
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        }
    }
    public function logoutSession($bot)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bot->token
            ])->post(env('APP_BOT_URL') . '/' . $bot->session_name . '/logout-session', [
                        'webook' => env('APP_CENTRAL_DOMAIN') . '/api/chat',
                        'waitQrCode' => true,
                    ]);
            $data = $response->json();
            if ($data['status'] !== 'Disconnected') {
                $this->logoutSession($bot);
            }
            $bot->phone_number = null;
            $bot->status = null;
            $bot->save();
            $result = [
                'status' => $response->status(),
                'data' => $data,
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => $response->status(),
                'data' => $response->json(),
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        }
    }
    public function getSessionStatus($session_name, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get(env('APP_BOT_URL') . '/' . $session_name . '/status-session', []);
            $data = $response->json();
            $result = [
                'status' => $response->status(),
                'data' => $data,
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => $response->status(),
                'data' => $response->json(),
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        }
    }
    public function getConnectionSession($session_name, $token)
    {
        $checkSessionStatus = $this->getSessionStatus($session_name, $token);
        if ($checkSessionStatus['status'] >= 200 && $checkSessionStatus['status'] < 300) {
            $bot = Bot::where('session_name', $session_name)->first();
            if (
                ($checkSessionStatus['data']['status'] != 'CLOSED' && !is_null($checkSessionStatus['data']['qrcode'])) ||
                ($checkSessionStatus['data']['status'] != 'CLOSED' && !is_null($bot->phone_number))
            ) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])->get(env('APP_BOT_URL') . '/' . $session_name . '/check-connection-session', []);
                    $data = $response->json();
                    $data['qrcode'] = $checkSessionStatus['data']['qrcode'];
                    $result = [
                        'status' => $response->status(),
                        'data' => $data,
                        'message' => $response->getReasonPhrase()
                    ];
                    return $result;
                } catch (\Exception $e) {
                    $result = [
                        'status' => $response->status(),
                        'data' => $response->json(),
                        'message' => $response->getReasonPhrase()
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'status' => 400,
                    'message' => "Session Closed"
                ];
            }
        } else {
            $message = $checkSessionStatus['message'];
            if (!is_null($checkSessionStatus['data']) && count($checkSessionStatus['data']) > 0) {
                if (isset($checkSessionStatus['data']['message'])) {
                    $message .= ' | ' . $checkSessionStatus['data']['message'];
                }
            }
            $result = [
                'status' => 400,
                'message' => $message
            ];
        }
        return $result;
    }
    public function getHostDevice($session_name, $token, $phone_number = null, $id = null)
    {
        $checkConnectionStatus = $this->getConnectionSession($session_name, $token);
        if ($checkConnectionStatus['status'] >= 200 && $checkConnectionStatus['status'] < 300) {
            if ($checkConnectionStatus['data']['status']) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])->get(env('APP_BOT_URL') . '/' . $session_name . '/host-device', []);
                    $data = $response->json();
                    $result = [
                        'status' => $response->status(),
                        'data' => $data,
                        'message' => $response->getReasonPhrase()
                    ];
                    if (isset($data['response'])) {
                        ;
                        if (is_null($phone_number) && !is_null($id)) {
                            $phone_number = $data['response']['phoneNumber'];
                            $bot = Bot::find($id);
                            if ($bot->phone_number !== $phone_number) {
                                $bot->phone_number = $phone_number;
                                $bot->status = 'connected';
                                $bot->save();
                            }
                        }

                    }
                    return $result;
                } catch (\Exception $e) {
                    $result = [
                        'status' => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                    return $result;
                }
            } else {
                $message = 'Phone ' . $checkConnectionStatus['data']['message'];
                if (isset($checkConnectionStatus['data']['qrcode'])) {
                    if (strpos($checkConnectionStatus['data']['qrcode'], "data:image/png") !== false) {
                        $message .= "<br><img src='{$checkConnectionStatus['data']['qrcode']}'>";
                    }
                }
                $result = [
                    'status' => 400,
                    'message' => $message,
                ];
            }
        } else {
            $message = $checkConnectionStatus['message'];
            if (isset($checkConnectionStatus['data'])) {
                if (!is_null($checkConnectionStatus['data']) && count($checkConnectionStatus['data']) > 0) {
                    if (isset($checkConnectionStatus['data']['message'])) {
                        $message .= ' | ' . $checkConnectionStatus['data']['message'];
                    }
                }
            }
            $result = [
                'status' => 400,
                'message' => $message
            ];
        }
        return $result;
    }

    public function setTyping($bot, $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bot->token
            ])->post(env('APP_BOT_URL') . '/' . $bot->session_name . '/typing', [
                        'phone' => $data['phone_number'],
                        'value' => $data['isTyping'] ?? false,
                        'isGroup' => $data['isGroup'] ?? false,
                    ]);
            $data = $response->json();
            if ($data['status'] !== 'success') {
                $this->setTyping($bot, $data);
            }
            $result = [
                'status' => $response->status(),
                'data' => $data,
                'message' => $response->getReasonPhrase()
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => 500,
                'data' => '',
                'message' => $e->getMessage()
            ];
            return $result;
        }
    }
    public function sendListMessage($bot, $data)
    {
        Log::info($data);
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bot->token
            ])->post(env('APP_BOT_URL') . '/' . $bot->session_name . '/send-list-message', [
                        'phone' => $data['phoneNumber'],
                        'description' => $data['description'],
                        'buttonText' => $data['buttonText'],
                        'isGroup' => $data['isGroup'] ?? false,
                        'sections' => [$data['sections']],
                    ]);
            $data = $response->json();
            switch ($data['status']) {
                case 'success':
                    $result = [
                        'status' => $response->status(),
                        'data' => $data,
                        'message' => $response->getReasonPhrase()
                    ];
                    break;
                default:
                    Log::error($data);
                    $result = [
                        'status' => $response->status(),
                        'data' => $data,
                        'message' => $response->getReasonPhrase()
                    ];
                    break;
            }
            return $result;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            $result = [
                'status' => 500,
                'data' => '',
                'message' => $e->getMessage()
            ];
            return $result;
        }
    }
    public function generateListMessage($bot, $contact, $autoReplies)
    {
        $data['phoneNumber'] = "{$contact->country_code}{$contact->contact_number}";
        $data['description'] = "Halo $contact->name, Kamu menghubungi layanan customer service {$bot->tenant->tenant_name}.\nKamu ingin terhubung dengan siapa nih?";
        $data['buttonText'] = "Pilih Layanan";
        Log::info($autoReplies);
        $formatting = [];
        foreach ($autoReplies as $key => $autoReply) {
            $i = [
                "rowId" => $autoReply->id . '-0',
                "title" => $autoReply->name,
                "description" => $autoReply->description
            ];
            $formatting[] = $i;
        }
        /* if ($first) {
        } else {
            foreach ($autoReplies as $key => $autoReply) {
                $i = [
                    "rowId" => $autoReply->auto_reply_id . '-' . $autoReply->id,
                    "title" => $autoReply->message,
                    "description" => $autoReply->description
                ];
                $formatting[] = $i;
            }
        } */

        $data['sections'] = [
            "title" => "List Layanan " /* . $autoReplies[0]->autoReplyHeader->name */ ,
            "rows" => $formatting
        ];
        $this->sendListMessage($bot, $data);
    }


    public function syncChat($bot)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot->token
        ])->get(env('APP_BOT_URL') . '/' . $bot->session_name . '/all-chats-with-messages', []);

        $response = $response->json();
        if ($response['status'] == 'success') {
            $data = $response['response'];
            $chunks = array_chunk($data, 2);
            foreach ($chunks as $chunk) {
                SyncChatJob::dispatch($chunk, $bot);
            }
        }
        return response()->json(['status' => 'Processing started']);
    }
    public function checkContact($bot, $phone_number)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot->token
        ])->get(env('APP_BOT_URL') . '/' . $bot->session_name . '/check-number-status/' . $phone_number, []);
        $response = $response->json();

        if ($response['response']['numberExists'] === false) {
            return false;
        }
        return true;
    }

    public function getContact($bot, $phone_number)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot->token
        ])->get(env('APP_BOT_URL') . '/' . $bot->session_name . '/contact/' . $phone_number, []);
        $response = $response->json();

        return $response;
    }

    public function getMessage($bot, $phone_number)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot->token
        ])->get(env('APP_BOT_URL') . '/' . $bot->session_name . '/get-messages/' . $phone_number, []);
        $response = $response->json();
        if ($response['status'] == 'error') {
            $response = false;
        }
        return $response;
    }
}
