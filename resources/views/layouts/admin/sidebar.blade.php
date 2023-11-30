<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Logo -->
    <div class="app-brand demo">
        <a href="/" class="app-brand-link">
            <img src="{{ siteLogo() }}" alt="Logo" class="app-brand-logo demo" style="height: 100%; width: 2.1rem">
            <span class="app-brand-text demo menu-text fw-bold" style="font-size: 1.25rem">{{ appName() }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <!-- Menu -->
    <ul class="menu-inner py-1">
        <li class="menu-item" id="dashboard">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-device-laptop"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Apps</span>
        </li>

        @foreach (getMenu() as $main)
            @if (canAccessMenu($main))
                <li class="menu-item">
                    <a href="@if ($main->url) {{ url($main->url) }} @else {{ __('#') }} @endif"
                        class="menu-link @if ($main->hasSubMenu()) menu-toggle @endif">
                        <i class="menu-icon tf-icons ti ti-{{ $main->icon }}"></i>
                        <div>{{ $main->name }}</div>
                    </a>

                    @if ($main->hasSubMenu())
                        <ul class="menu-sub">
                            @foreach ($main->subMenu as $sub)
                                @if (
                                    !$sub->permission ||
                                        auth()->user()->can($sub->permission->name))
                                    <li class="menu-item">
                                        <a href="@if ($sub->url) {{ url($sub->url) }} @else {{ __('#') }} @endif"
                                            class="menu-link">
                                            <div>{{ $sub->name }}</div>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</aside>
