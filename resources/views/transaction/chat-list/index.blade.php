@extends('layouts.admin.app')
@section('title', 'ChatList')

@push('style')
    <link rel="stylesheet" href="{{ asset('theme/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/app-assets/css/pages/app-chat.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/app-assets/css/pages/app-chat-list.min.css') }}" />
@endpush

@push('script')
    <script src="{{ asset('theme/app-assets/js/scripts/pages/app-chat.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
@endpush

@section('content')
    <div class="chat-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0">
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
            <div class="content-right">
                <div class="content-wrapper container-xxl p-0">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <div class="body-content-overlay"></div>
                        <!-- Main chat area -->
                        <section class="chat-app-window">
                            <!-- To load Conversation -->
                            <div class="start-chat-area">
                                <div class="mb-1 start-chat-icon">
                                    <i class="fa fa-message"></i>
                                </div>
                                <h4 class="sidebar-toggle start-chat-text">Start Conversation</h4>
                            </div>
                            <!--/ To load Conversation -->

                            <!-- Active Chat -->
                            <div class="active-chat d-none">
                                <!-- Chat Header -->
                                <div class="chat-navbar">
                                    <header class="chat-header">
                                        <div class="d-flex align-items-center">
                                            <div class="sidebar-toggle d-block d-lg-none me-1">
                                                <i data-feather="menu" class="font-medium-5"></i>
                                            </div>
                                            <div class="avatar avatar-border user-profile-toggle m-0 me-1">
                                                <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-7.jpg') }}"
                                                    alt="avatar" height="36" width="36" />
                                                <span class="avatar-status-busy"></span>
                                            </div>
                                            <h6 class="mb-0">Kristopher Candy</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="phone-call"
                                                class="cursor-pointer d-sm-block d-none font-medium-2 me-1"></i>
                                            <i data-feather="video"
                                                class="cursor-pointer d-sm-block d-none font-medium-2 me-1"></i>
                                            <i data-feather="search"
                                                class="cursor-pointer d-sm-block d-none font-medium-2"></i>
                                            <div class="dropdown">
                                                <button
                                                    class="btn-icon btn btn-transparent hide-arrow btn-sm dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i data-feather="more-vertical" id="chat-header-actions"
                                                        class="font-medium-2"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="chat-header-actions">
                                                    <a class="dropdown-item" href="#">View Contact</a>
                                                    <a class="dropdown-item" href="#">Mute Notifications</a>
                                                    <a class="dropdown-item" href="#">Block Contact</a>
                                                    <a class="dropdown-item" href="#">Clear Chat</a>
                                                    <a class="dropdown-item" href="#">Report</a>
                                                </div>
                                            </div>
                                        </div>
                                    </header>
                                </div>
                                <!--/ Chat Header -->

                                <!-- User Chat messages -->
                                <div class="user-chats">
                                    <div class="chats">
                                        <div class="chat">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>How can we help? We're here for you! 😄</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chat chat-left">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-7.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>Hey John, I am looking for the best admin template.</p>
                                                    <p>Could you please help me to find it out? 🤔</p>
                                                </div>
                                                <div class="chat-content">
                                                    <p>It should be Bootstrap 4 compatible.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="divider">
                                            <div class="divider-text">Yesterday</div>
                                        </div>
                                        <div class="chat">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>Absolutely!</p>
                                                </div>
                                                <div class="chat-content">
                                                    <p>Vuexy admin is the responsive bootstrap 4 admin template.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chat chat-left">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-7.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>Looks clean and fresh UI. 😃</p>
                                                </div>
                                                <div class="chat-content">
                                                    <p>It's perfect for my next project.</p>
                                                </div>
                                                <div class="chat-content">
                                                    <p>How can I purchase it?</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chat">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>Thanks, from ThemeForest.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chat chat-left">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-7.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>I will purchase it for sure. 👍</p>
                                                </div>
                                                <div class="chat-content">
                                                    <p>Thanks.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chat">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    <img src="{{ asset('theme/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                                        alt="avatar" height="36" width="36" />
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p>Great, Feel free to get in touch on</p>
                                                </div>
                                                <div class="chat-content">
                                                    <p>https://pixinvent.ticksy.com/</p>
                                                </div>
                                            </div>
                                        </div>
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
        </div>
    </div>
    {{-- @livewire('transaction.show-chat-list', ['title' => $__env->yieldContent('title')]) --}}
@endsection
