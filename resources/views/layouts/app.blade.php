<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pO8zCpCMX9V+4+4+…TUZwgfmxmYF1yLgXw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet"/>

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- Tu archivo app.css -->
    <link href="/css/app.css" rel="stylesheet">

    <style>
        /* Sidebar fixed layout for all panels (admin, minibar, recepcion, habitaciones...) */
        :root {
            --sidebar-width: 18rem; /* default width */
        }

        /* override width using variable and keep fixed positioning */
        .admin-sidebar-fixed {
            position: fixed !important;
            top: 0;
            left: 0;
            bottom: 0;
            /* slightly narrower so visual gap is reduced but layout doesn't overlap */
            width: calc(var(--sidebar-width) - 0.25rem) !important;
            z-index: 1030;
            overflow-y: auto;
            padding-top: 1rem;
        }

        /* also force width when Bootstrap column classes are present */
        .admin-sidebar-fixed.col-auto,
        .admin-sidebar-fixed.col-md-3,
        .admin-sidebar-fixed.col-xl-2 {
            flex: 0 0 auto;
            width: var(--sidebar-width) !important;
        }

        .admin-content {
            /* subtract bootstrap column padding (1rem each side) to eliminate gap */
            margin-left: calc(var(--sidebar-width) - 2rem);
            padding-left: 0; /* remove default .col padding on left */
        }

        /* panel-specific variable overrides */
        body.panel-habitaciones { --sidebar-width: 22rem; }
        body.panel-minibar     { --sidebar-width: 20rem; }
        body.panel-admin       { --sidebar-width: 18rem; }
        body.panel-recepcion   { --sidebar-width: 18rem; }

        /* container and row should not add extra padding when sidebar fixed */
        .container-fluid { padding-left: 0; padding-right: 0; }
        .row.flex-nowrap { margin-left: 0; margin-right: 0; }

        /* Make inner .container sit left and use remaining width next to sidebar
           so centered max-width containers don't create a visual gap. */
        body.panel-habitaciones .admin-content .container,
        body.panel-minibar .admin-content .container,
        body.panel-admin .admin-content .container,
        body.panel-recepcion .admin-content .container {
            margin-left: 0 !important;
            max-width: calc(100% - var(--sidebar-width));
            padding-left: 1rem; /* small inner padding */
            padding-right: 1rem;
        }

        /* Responsive reset on very small screens */
        @media (max-width: 767.98px) {
            .admin-sidebar-fixed {
                position: relative !important;
                width: 100%;
            }

            .admin-content { margin-left: 0; }
        }
    </style>

    <title>Hotel Management System</title>
</head>
@php
    // determine panel-specific body class for sidebar width
    $bodyClasses = [];
    if(request()->routeIs('admin.habitaciones.*')) {
        $bodyClasses[] = 'panel-habitaciones';
    } elseif(request()->routeIs('admin.minibar.*')) {
        $bodyClasses[] = 'panel-minibar';
    } elseif(request()->routeIs('admin.*')) {
        $bodyClasses[] = 'panel-admin';
    } elseif(request()->routeIs('reception.*')) {
        $bodyClasses[] = 'panel-recepcion';
    }
@endphp
<body class="{{ implode(' ', $bodyClasses) }}">
        {{-- ✅ Notificación Global Única --}}
        @if (session('success'))
            <div id="global-notification" style="
                position: fixed;
                top: 80px;
                right: 20px;
                background-color: #38c172;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 9999;
                font-weight: bold;">
                {{ session('success') }}
            </div>
            <script src="{{ asset('js/notificaciones.js') }}"></script>
        @endif

    <!-- Admin Panel -->
    @if(isset($adminView))
        <div class="container-fluid">
            <div class="row flex-nowrap">
                @if(isset($sidebarView))
                    @include($sidebarView)
                @else
                    @include('admin.sidebar')
                @endif
                <div class="col py-3 admin-content">
                    @yield('content')
                </div>
            </div>
        </div>
    @else
        <!-- Default App View -->
        <div class="bg-white p-0" style="margin: 0; padding: 0; width: 100%;">
            <!-- Spinner loading  -->
            @include('components.spinner')
            <!-- Header -->
            @yield('header')

            <!-- Content -->
            @yield('content')
            <!-- Footer -->
            @yield('footer')
        </div>
    @endif

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>

