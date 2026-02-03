<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base bx bx-calendar-check"></i>
                    <div data-i18n="Booking">Booking</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('booking.meeting-room.index') }}" class="menu-link">
                            <i class="menu-icon icon-base bx bx-door-open"></i>
                            <div data-i18n="Meeting Room">Meeting Room</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('booking.driver.index') }}" class="menu-link" target="_blank">
                            <i class="menu-icon icon-base bx bx-car"></i>
                            <div data-i18n="Driver">Driver</div>
                        </a>
                    </li>
                </ul>
            </li>
            @if( auth()->user()->isAdmin() )

            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base bx bx-cog"></i>
                    <div data-i18n="Admin">Admin</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.locations.index') }}" class="menu-link">
                            <i class="menu-icon icon-base bx bx-map"></i>
                            <div data-i18n="Locations">Locations</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="{{ route('admin.meeting-rooms.index') }}" class="menu-link">
                            <i class="menu-icon icon-base bx bx-door-open"></i>
                            <div data-i18n="Setting Meeting Room">Setting Meeting Room</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="{{ route('admin.drivers.index') }}" class="menu-link">
                            <i class="menu-icon icon-base bx bx-car"></i>
                            <div data-i18n="Setting Driver">Setting Driver</div>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </div>
</aside>