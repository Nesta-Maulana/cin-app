<?php

namespace App\Jobs;

use App\Repositories\Master\Contact\ContactRepositoryInterface;
use App\Repositories\Transaction\ChatRoom\ChatRoomRepositoryInterface;
use App\Repositories\Transaction\ChatRoomDetail\ChatRoomDetailRepositoryInterface;
use App\Services\BotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SyncChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chunk, $bot, $botService;
    protected $contactRepository, $chatRoomRepository, $chatRoomDetailRepository;

    public function __construct(array $chunk, $bot)
    {
        $this->chunk = $chunk;
        $this->bot = $bot;
        $this->botService = app(BotService::class);
        $this->contactRepository = app(ContactRepositoryInterface::class);
        $this->chatRoomRepository = app(ChatRoomRepositoryInterface::class);
        $this->chatRoomDetailRepository = app(ChatRoomDetailRepositoryInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->chunk as $key => $chunk) {
            $phone_number = explode("@", $chunk['id']);
            if ($phone_number[1] !== 'g.us') {
                $phone_number = $phone_number[0];
                if ($this->botService->checkContact($this->bot, $phone_number)) {
                    $contact_data = $this->contactRepository->contactFromBot($this->bot, $phone_number);
                    $chat_room_data = [
                        'contact_id' => $contact_data->id,
                        'unread_count' => $chunk['unreadCount']
                    ];
                    $chat_room = (is_null($contact_data->chatRoom)) ? $this->chatRoomRepository->create($chat_room_data) : $this->chatRoomRepository->update($contact_data->chatRoom->id, $chat_room_data);
                    $message = $this->botService->getMessage($this->bot, $phone_number);
                    if ($message) {
                        foreach ($message['response'] as $key_message => $msg) {
                            $chat_room_detail = $this->chatRoomDetailRepository->storeMessage($chat_room, $msg);
                        }
                    }
                }
            }
        }
    }
}
