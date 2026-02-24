<!doctype html>

<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default" data-assets-path="assets/" data-template="{{ config('app.layout') === 'horizontal' ? 'horizontal-menu-template-no-customizer' : 'vertical-menu-template-no-customizer' }}" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>LGI - GS Booking System</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/notyf/notyf.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" hre="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/booking-detail.css') }}">

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    @livewireStyles
    @yield('style')
</head>

<body>
    <style>
        #loading {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('{{ asset(' assets/loading-content.gif') }}') 50% 50% no-repeat rgb(249, 249, 249);
            opacity: 0.5;
        }

        a {
            color: black;
        }
    </style>

    <div id="loading" style="display:none;"></div>

    @if(config('app.layout') === 'horizontal')

        {{-- ==================== HORIZONTAL LAYOUT ==================== --}}
        <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
            <div class="layout-container">
                <nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
                    <div class="container-xxl">
                        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                            <a href="{{ route('home') }}" class="app-brand-link gap-2">
                                <span class="app-brand-text demo menu-text fw-bold text-heading d-flex flex-column align-items-center">
                                    <span>LGI</span>
                                    <small style="font-size: 13px; font-weight: normal;">
                                        a company of Hanwha
                                    </small>
                                </span>
                            </a>

                            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                                <i class="icon-base bx bx-chevron-left d-flex align-items-center justify-content-center"></i>
                            </a>
                        </div>

                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                                <i class="icon-base bx bx-menu icon-md"></i>
                            </a>
                        </div>

                        <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <span class="avatar-initial rounded-circle bg-primary">
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-online">
                                                            <span class="avatar-initial rounded-circle bg-primary">
                                                                {{ auth()->user()->initials() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ auth()->user()->Name }}</h6>
                                                        <small class="text-body-secondary">
                                                            {{ auth()->user()->role() }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="post">
                                                @csrf
                                                <button class="dropdown-item" type="submit">
                                                    <i class="icon-base bx bx-power-off icon-md me-3"></i>
                                                    <span>Log Out</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="layout-page">
                    <div class="content-wrapper">
                        @include('layouts.navbar-horizontal')

                        <div class="container-xxl flex-grow-1 container-p-y">
                            <span>
                                <ol class="breadcrumb mt-2">
                                    @php
                                    $SegmentCount = count(Request()->segments());

                                    if( $SegmentCount > 0 ){
                                        for( $segment = 1; $segment <= $SegmentCount; $segment++ ){
                                            $Segments[]=ucwords(str_replace('-', ' ' , Request()->segment($segment)));
                                        }
                                    }
                                    @endphp
                                    @if( isset($Segments) && count($Segments) > 0 )
                                        @php
                                            $i = 0;
                                            $SegmentCount = count($Segments);
                                        @endphp
                                        <li class="breadcrumb-item">
                                            <b class="text-primary">
                                                Home
                                            </b>
                                        </li>
                                        @foreach ($Segments as $key => $val)
                                            @switch($val)
                                                @case('1') {{-- 1 Means Archived --}}
                                                    <li class="breadcrumb-item">
                                                        <b class="text-primary">
                                                            Archived
                                                        </b>
                                                    </li>
                                                @break
                                                @case('0') {{-- 0 Means not archived --}}
                                                @break
                                                @default
                                                    <li class="breadcrumb-item">
                                                        <b class="text-primary">
                                                            {{ $val }}
                                                        </b>
                                                    </li>
                                                @break
                                            @endswitch
                                        @endforeach
                                    @endif
                                </ol>
                            </span>

                            @yield('content')
                        </div>

                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>

    @else

        {{-- ==================== VERTICAL LAYOUT (default) ==================== --}}
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">

                <aside id="layout-menu" class="layout-menu menu-vertical menu">
                    <div class="app-brand demo">
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            <span class="app-brand-text demo menu-text fw-bold text-heading d-flex flex-column align-items-center">
                                <span>LGI</span>
                                <small style="font-size: 13px; font-weight: normal;">
                                    a company of Hanwha
                                </small>
                            </span>
                        </a>

                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                            <i class="icon-base bx bx-chevron-left"></i>
                        </a>
                    </div>

                    <div class="menu-inner-shadow"></div>

                    @include('layouts.navbar')
                </aside>

                <div class="menu-mobile-toggler d-xl-none rounded-1">
                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                        <i class="bx bx-menu icon-base"></i>
                        <i class="bx bx-chevron-right icon-base"></i>
                    </a>
                </div>

                <div class="layout-page">
                    <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                        id="layout-navbar">
                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                                <i class="icon-base bx bx-menu icon-md"></i>
                            </a>
                        </div>

                        <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                            <div class="navbar-nav align-items-center">
                                <div class="nav-item navbar-search-wrapper mb-0">
                                    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                                        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
                                    </a>
                                </div>
                            </div>
                            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                        data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <span class="avatar-initial rounded-circle bg-primary">
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="pages-account-settings-account.html">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-online">
                                                            <span class="avatar-initial rounded-circle bg-primary">
                                                                {{ auth()->user()->initials() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">
                                                            {{ auth()->user()->Name }}
                                                        </h6>
                                                        <small class="text-body-secondary">
                                                            {{ auth()->user()->role() }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider my-1"></div>
                                        </li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="post">
                                                @csrf
                                                <button class="dropdown-item" type="submit">
                                                    <i class="icon-base bx bx-power-off icon-md me-3"></i>
                                                    <span>Log Out</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            @yield('content')
                        </div>

                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>

            <div class="layout-overlay layout-menu-toggle"></div>
            <div class="drag-target"></div>
        </div>

    @endif

    @include('layouts.footer')

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-crm.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/app-calendar-events.js') }}"></script>
    <script src="{{ asset('assets/js/app-calendar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/notyf/notyf.js') }}"></script>
    <script src="{{ asset('assets/custom-toastr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    @yield('script')

    <script>
        let sessionSuccess = @js(session('success'));
        let sessionError = @js($errors->all());
        let sessionInfo = @js(session('info'));
        let sessionWarning = @js(session('warning'));

        $(document).ready(function(){
            if( sessionSuccess ){
                notyf.open({
                    type: 'success',
                    message: sessionSuccess
                });
            }

            if( sessionError ){
                sessionError.forEach(err => {
                    notyf.open({
                        type: 'error',
                        message: err
                    });
                });
            }

            if( sessionInfo ){
                notyf.open({
                    type: 'info',
                    message: sessionInfo
                });
            }

            if( sessionWarning ){
                notyf.open({
                    type: 'warning',
                    message: sessionWarning
                });
            }

            window.addEventListener('show-error', event => {
                event.detail.forEach(err => {
                    notyf.open({
                        type: 'error',
                        message: err.message
                    });
                });
            });

            window.addEventListener('show-success', event => {
                event.detail.forEach(err => {
                    notyf.open({
                        type: 'success',
                        message: err.message
                    });
                });
            });

        });
    </script>

    @livewireScripts
</body>

</html>
