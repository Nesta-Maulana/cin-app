<div class="content-right">
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="body-content-overlay"></div>
            <!-- Main chat area -->
            <section class="chat-app-window">
                <!-- To load Conversation -->
                <div class="start-chat-area d-none">
                    <div class="mb-1 start-chat-icon">
                        <i class="fa fa-message"></i>
                    </div>
                    <h4 class="sidebar-toggle start-chat-text">Start Conversation</h4>
                </div>
                <!--/ To load Conversation -->
                <!-- Active Chat -->
                <div class="active-chat">
                    <!-- Chat Header -->
                    <div class="chat-navbar">
                        <header class="chat-header">
                            <div class="d-flex align-items-center">
                                <div class="sidebar-toggle d-block d-lg-none me-1">
                                    <i data-feather="menu" class="font-medium-5"></i>
                                </div>
                                <input type="hidden" id="chat_room_id" value="{{ $chat[0]->chatRoom->id }}">
                                <button wire:click="syncRoomChat" class="btn btn-sm btn-label-primary hidden"
                                    id="syncRoomChat">
                                    <i class="fa-solid fa-refresh fa-xs me-1"></i>
                                    Sync
                                </button>
                                <div class="avatar avatar-border user-profile-toggle m-0 me-1">
                                    <img src="@if (isset($chat[0]->chatRoom->contact->data['profilePicThumbObj']['img'])) {{ $chat[0]->chatRoom->contact->data['profilePicThumbObj']['img'] }}
                                    @else {{ asset('default-user.jpg') }} @endif"
                                        alt="avatar" height="36" width="36" />
                                    {{-- <span class="avatar-status-busy"></span> --}}
                                </div>
                                <h6 class="mb-0">{{ $chat[0]->chatRoom->contact->name }}</h6>
                            </div>
                        </header>
                    </div>
                    <!--/ Chat Header -->

                    <!-- User Chat messages -->
                    <div class="user-chats ps ps--active-y" style="overflow:none !important">
                        <div class="chats" style="overflow:scroll !important">
                            @php
                                $last_date = formatDate($chat[0]->message_time);
                            @endphp
                            <div class="divider">
                                <div class="divider-text">{{ $last_date }}</div>
                            </div>
                            @foreach ($chat as $item)
                                @if (formatDate($item->message_time) !== $last_date)
                                    @php
                                        $last_date = formatDate($item->message_time);
                                    @endphp
                                    <div class="divider">
                                        <div class="divider-text">{{ $last_date }}</div>
                                    </div>
                                @endif
                                @if ($item->is_from_me)
                                    <div class="chat">
                                        <div class="chat-avatar">
                                            <span class="avatar box-shadow-1 cursor-pointer">
                                                <img src="{{ asset('bot.png') }}" alt="avatar" height="36"
                                                    width="36" />
                                            </span>
                                        </div>
                                        <div class="chat-body">
                                            <div class="chat-content">
                                                <p>{{ $item->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="chat chat-left">
                                        <div class="chat-avatar">
                                            <span class="avatar box-shadow-1 cursor-pointer">
                                                <img src="@if (isset($item->chatRoom->contact->data['profilePicThumbObj']['img'])) {{ $item->chatRoom->contact->data['profilePicThumbObj']['img'] }}
                                                @else {{ asset('default-user.jpg') }} @endif"
                                                    alt="avatar" height="36" width="36" />
                                            </span>
                                        </div>
                                        <div class="chat-body">
                                            <div class="chat-content">
                                                <p>{{ $item->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <input type="hidden" name="last_updated_chat" id="last_updated_chat"
                                value="{{ formatDateTimeJKT($item->message_time) }}">
                        </div>
                    </div>
                    <!-- User Chat messages -->

                    <!-- Submit Chat form -->
                    <form class="chat-app-form" action="javascript:void(0);" onsubmit="enterChat();">
                        <div class="input-group input-group-merge me-1 form-send-message">
                            <span class="speech-to-text input-group-text">
                                <i class="cursor-pointer fa fa-microphone-lines"></i>
                            </span>
                            <input type="text" class="form-control message"
                                placeholder="Type your message or use speech to text" />
                            <span class="input-group-text">
                                <label for="attach-doc" class="attachment-icon form-label mb-0">
                                    <i class="cursor-pointer text-secondary fa fa-file"></i>
                                    <input type="file" id="attach-doc" hidden /> </label></span>
                        </div>
                        <button type="button" class="btn btn-primary send" onclick="enterChat();">
                            <i data-feather="send" class="d-lg-none"></i>
                            <span class="d-none d-lg-block">Send</span>
                        </button>
                    </form>
                    <!--/ Submit Chat form -->
                </div>
                <!--/ Active Chat -->
            </section>
            <!--/ Main chat area -->


        </div>
    </div>
</div>
<script>
    function scrollToBottom() {
        userChats = $('.user-chats'),
            userChats.animate({
                scrollTop: userChats[0].scrollHeight
            }, 400);
    }

    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
    });

    Livewire.hook('message.processed', (message, component) => {
        scrollToBottom();
    });
    window.addEventListener('sync-room-chat-complete', event => {
        scrollToBottom();
    });
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('4b399ecfe9e3ad045af5', {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe("updated_chat");
    channel.bind("updated_room_chat", function(data) {
        if (data.chat_room_id !== $('#chat_room_id').val()) {
            if (data.last_updated !== $('#last_updated_chat').val()) {
                $('#syncRoomChat').click();
            }
        }
    });
</script>
