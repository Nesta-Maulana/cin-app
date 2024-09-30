<div class="sidebar-left">
    <div class="sidebar">
        <!-- Admin user profile area -->
        <div class="chat-profile-sidebar">
            <header class="chat-profile-header">
                <span class="close-icon">
                    <i data-feather="x"></i>
                </span>
                <!-- User Information -->
                <div class="header-profile-sidebar">
                    <div class="avatar box-shadow-1 avatar-xl avatar-border">
                        <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                            alt="user_avatar" />
                        <span class="avatar-status-online avatar-status-xl"></span>
                    </div>
                    <h4 class="chat-user-name">John Doe</h4>
                    <span class="user-post">Admin</span>
                </div>
                <!--/ User Information -->
            </header>
        </div>
        <!--/ Admin user profile area -->

        <!-- Chat Sidebar area -->
        <div class="sidebar-content">
            <span class="sidebar-close-icon">
                <i class="fa fa-x"></i>
            </span>
            <!-- Sidebar header start -->
            <div class="chat-fixed-search">
                <div class="d-flex align-items-center w-100">
                    <div class="sidebar-profile-toggle">
                        <div class="avatar avatar-border">
                            <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                alt="user_avatar" height="42" width="42" />
                            <span class="avatar-status-online"></span>
                        </div>
                    </div>
                    {{-- <div class="input-group input-group-merge ms-1 w-100">
                        <span class="input-group-text round"><i data-feather="search"
                                class="text-muted"></i></span>
                        <input type="text" class="form-control round" id="chat-search"
                            placeholder="Search or start a new chat" aria-label="Search..."
                            aria-describedby="chat-search" />
                    </div> --}}
                </div>
            </div>
            <!-- Sidebar header end -->

            <!-- Sidebar Users start -->
            <div id="users-list" class="chat-user-list-wrapper list-group">
                <div class="row">
                    <div class="col-6">
                        <h4 class="chat-list-title">Chats</h4>
                    </div>
                    <div class="col-6 text-end px-2">
                        <a class="btn btn-sm btn-primary my-2" href="{{ route('bot.sync-chat') }}">
                            <i class="fa fa-sync"></i> Sync Chat
                        </a>
                        <button wire:click="syncChat" class="btn btn-sm btn-label-primary hidden" id="syncChat">
                            <i class="fa-solid fa-refresh fa-xs me-1"></i>
                            Sync
                        </button>
                    </div>
                </div>
                <ul class="chat-users-list chat-list media-list ps ps--active-x ps--active-y"
                    style="overflow:scroll !important">
                    <input type="hidden" name="last_updated" id="last_updated" value="{{ $last_updated }}">
                    @foreach ($chats as $chat)
                        <li>
                            <span class="avatar">
                                <img src="@if (isset($chat->contact->data['profilePicThumbObj']['img'])) {{ $chat->contact->data['profilePicThumbObj']['img'] }}
                                    @else {{ asset('default-user.jpg') }} @endif"
                                    height="42" width="42" alt="Generic placeholder image" />
                                {{-- <span class="avatar-status-offline"></span> --}}
                            </span>
                            <div class="chat-info flex-grow-1">
                                <h5 class="mb-0">{{ $chat->contact->name }}</h5>
                                <p class="card-text text-truncate">
                                    @if ($chat->chatDetail[0]->type == 'chat')
                                        {{ strlen($chat->chatDetail[0]->message) > 20 ? substr($chat->chatDetail[0]->message, 0, 20) . '...' : $chat->chatDetail[0]->message }}
                                    @else
                                        <i class="fa fa-file"></i>
                                        @if (isset($chat->chatDetail[0]->message) && !is_null($chat->chatDetail[0]->message))
                                            {{ strlen($chat->chatDetail[0]->message) > 20 ? substr($chat->chatDetail[0]->message, 0, 20) . '...' : $chat->chatDetail[0]->message }}
                                        @else
                                            Media
                                        @endif
                                    @endif
                                </p>
                            </div>
                            <div class="chat-meta text-nowrap">
                                <small
                                    class="float-end mb-25 chat-time">{{ formatDateTime($chat->chatDetail[0]->message_time) }}</small>
                                @if ($chat->unread_count > 0)
                                    <span class="badge bg-danger rounded-pill float-end">
                                        {{ $chat->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Sidebar Users end -->
        </div>
        <!--/ Chat Sidebar area -->

    </div>
</div>
<script>
    function syncChat() {
        Livewire.emit('render');
    }
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('4b399ecfe9e3ad045af5', {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('last_updated_chat');
    channel.bind('last_updated', function(data) {
        if (data.last_updated !== $('#last_updated').val()) {
            $('#syncChat').click();
        }
    });
</script>
