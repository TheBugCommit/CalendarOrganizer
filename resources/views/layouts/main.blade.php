<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Calendar Organizer')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/css/selectize.css">
    <link rel="stylesheet" href="/css/styles.css">
    @yield('css')
</head>

<body id="body-pd">

    @if (Request::path() != 'login' && Request::path() != 'signup')
        <header class="header" id="header">
            <div class="header_toggle"> <i class="fas fa-bars" id="header-toggle"></i> </div>
        </header>
        <div class="l-navbar" id="nav-bar">
            <nav class="nav">
                <div>
                    <a href="{{ route('dashboard') }}" class="nav_logo"> <img src="/img/logo-light.webp"
                            class="nav_logo-icon" />
                        <span class="nav_logo-name">Calendar Organizer</span> </a>
                    <div class="nav_list">
                        <a href="{{ route('dashboard') }}"
                            class="nav_link {{ Request::path() == '/' ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt nav_icon"></i>
                            <span class="nav_name">Dashboard</span> </a>

                        <a href="{{ route('user.category.index') }}"
                            class="nav_link {{ Request::path() == 'categories' ? 'active' : '' }}">
                            <i class="fas fa-th-large nav_icon"></i>
                            <span class="nav_name">Categories</span>
                        </a>
                        <a href="{{ route('export.events') }}"
                            class="nav_link {{ Request::path() == 'export_events' ? 'active' : '' }}">
                            <i class="fas fa-download nav_icon"></i>
                            <span class="nav_name">Export Events</span>
                        </a>
                        <a href="{{ route('jasperreport.index') }}"
                            class="nav_link {{ Request::path() == 'jasperreport' ? 'active' : '' }}">
                            <i class="fas fa-file-invoice nav_icon"></i>
                            <span class="nav_name">Report JasperReport</span>
                        </a>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="nav_link"> <i class="fas fa-sign-out-alt nav_icon"></i>
                    <span class="nav_name">SignOut</span> </a>
            </nav>
        </div>

        <div class="container main-container">
            <div class="card">
                @yield('card-header')
                <div class="card-body position-relative">
                    <main id="app" >
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    @else
        <main id="app">
            @yield('content')
        </main>
    @endif


    <footer>@yield('footer')</footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="/js/selectize.min.js"></script>
    @yield('js')
    <script src="/js/app.js"></script>
</body>

</html>
