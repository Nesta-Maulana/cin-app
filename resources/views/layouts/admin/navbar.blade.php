<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <li class="nav-item d-none d-lg-block">
                <a class="nav-link nav-link-style">
                    <i class="fa-solid fa-moon fa-lg"></i>
                </a>
            </li>
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link"
                    id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">
                            {{ ucwords(auth()->user()->name) }}
                        </span>
                        <span class="user-status">
                            {{ ucwords(auth()->user()->getRoleNames()->first()) }}
                        </span>
                    </div>
                    <span class="avatar">
                        <span class="avatar-initial rounded-circle bg-primary bg-glow"
                            style="height: 40px; width: 40px;line-height: 40px;">
                            {{ myInitial() }}
                        </span>
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{ route('my-account.edit') }}">
                        <i class="me-50 fa-solid fa-user-gear"></i> Profile
                    </a>
                    <a class="dropdown-item" href="{{ route('my-account.log-activity') }}">
                        <i class="me-50 fa-solid fa-fingerprint"></i> Log Activity
                    </a>
                    <div class="dropdown-divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <a class="dropdown-item" type="submit" onclick="$('#logout-form').submit()">
                            <i class="me-50 fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </form>

                </div>
            </li>
        </ul>
    </div>
</nav>
