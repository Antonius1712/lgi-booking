<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
            <div class="container-xxl">
                <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                    <a href="{{ route('home') }}" class="app-brand-link gap-2">
                        <span
                            class="app-brand-text demo menu-text fw-bold text-heading d-flex flex-column align-items-center">
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