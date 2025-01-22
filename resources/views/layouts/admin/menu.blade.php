<div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
        {{-- <li class=" nav-item" id="dashboard">
            <a class="d-flex align-items-center menu-link" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-house"></i>
                <span class="menu-title text-truncate" data-i18n="Dashboard">
                    Dashboard
                </span>
            </a>
        </li> --}}
        {{-- <li class=" navigation-header">
            <span>List Menu</span>
            <i class="fa-solid fa-ellipsis"></i>
        </li> --}}
        @foreach (getMenu() as $main)
            @if (canAccessMenu($main))
                <li class="nav-item">
                    <a class="d-flex align-items-center menu-link"
                        href="@if($main->url){{url($main->url)}}@else{{__('#') }}@endif">
                        <i class="fa-solid {{ $main->icon }}"></i>
                        <span class="menu-title text-truncate"
                            data-i18n="{{ $main->name }}">{{ $main->name }}</span>
                    </a>
                    @if ($main->hasSubMenu())
                        <ul class="menu-content">
                            @foreach ($main->subMenu as $sub)
                                @if (
                                    !$sub->permission ||
                                        auth()->user()->can($sub->permission->name))
                                    <li>
                                        <a class="d-flex align-items-center menu-link"
                                            href="@if($sub->url){{url($sub->url)}}@else{{__('#')}}@endif">
                                            <i class="fa-solid {{ $sub->icon }}"></i>
                                            <span class="menu-item text-truncate"
                                                data-i18n="{{ $sub->name }}">{{ $sub->name }}</span>
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
</div>
