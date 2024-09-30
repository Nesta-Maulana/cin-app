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
                    </div>
                </div>
                <ul class="chat-users-list chat-list media-list">
                    <li>
                        <span class="avatar"><img
                                src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-3.jpg') }}"
                                height="42" width="42" alt="Generic placeholder image" />
                            <span class="avatar-status-offline"></span>
                        </span>
                        <div class="chat-info flex-grow-1">
                            <h5 class="mb-0">Elizabeth Elliott</h5>
                            <p class="card-text text-truncate">
                                Cake pie jelly jelly beans. Marzipan lemon drops halvah cake. Pudding cookie
                                lemon drops icing
                            </p>
                        </div>
                        <div class="chat-meta text-nowrap">
                            <small class="float-end mb-25 chat-time">4:14 PM</small>
                            <span class="badge bg-danger rounded-pill float-end">3</span>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- Sidebar Users end -->
        </div>
        <!--/ Chat Sidebar area -->

    </div>
</div>
