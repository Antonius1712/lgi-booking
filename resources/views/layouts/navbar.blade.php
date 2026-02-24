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
                <a href="{{ route('booking.driver.index') }}" class="menu-link">
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

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base bx bx-cart-alt"></i>
                    <div data-i18n="Bookings">Bookings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.driver-bookings.index') }}" class="menu-link">
                            <div data-i18n="Driver Booking">Driver Booking</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.meeting-room-bookings.index') }}" class="menu-link">
                            <div data-i18n="Meeting Room Booking">Meeting Room Booking</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.email-templates.index') }}" class="menu-link">
                    <i class="menu-icon icon-base bx bx-envelope"></i>
                    <div data-i18n="Email Templates">Email Templates</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.settings.index') }}" class="menu-link">
                    <i class="menu-icon icon-base bx bx-slider-alt"></i>
                    <div data-i18n="Configuration">Configuration</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.feedback-tags.index') }}" class="menu-link">
                    <i class="menu-icon icon-base bx bx-purchase-tag"></i>
                    <div data-i18n="Feedback Tags">Feedback Tags</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.export.index') }}" class="menu-link">
                    <i class="menu-icon icon-base bx bx-bar-chart-alt-2"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            </li>
    </li>
    @endif
</ul>