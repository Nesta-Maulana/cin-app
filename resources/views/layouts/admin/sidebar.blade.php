<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="javascript:void(0)">
                    <span class="brand-logo">
                        <img src="{{ siteLogo() }}" alt="Logo" {{-- class="app-brand-logo demo" style="height: 100%; width: 2.1rem" --}}>
                    </span>
                    <h2 class="brand-text">
                        @php
                            $app_name = appName();
                            $check_word = explode(' ', $app_name);
                            if (count($check_word) > 1) {
                                $app_name = str_replace(' ', '<br />', $app_name);
                            }
                            echo $app_name;
                        @endphp
                    </h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <hr>
    <div class="shadow-bottom"></div>
    @include('layouts.admin.menu')
</div>
