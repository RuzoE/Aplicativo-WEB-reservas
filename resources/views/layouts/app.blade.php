<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            /* Use exact sidebar width for margin to prevent overlap */
            margin-left: var(--sidebar-width);
            padding-left: 1.5rem; /* add visual gap between sidebar and content */
            padding-right: 1.5rem;
        }

        /* panel-specific variable overrides */
        body.panel-habitaciones { --sidebar-width: 22rem; }
        body.panel-minibar     { --sidebar-width: 22rem; }
        body.panel-admin       { --sidebar-width: 22rem; }
        body.panel-recepcion   { --sidebar-width: 22rem; }

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
            .row.flex-nowrap {
                flex-wrap: wrap !important;
            }

            .admin-sidebar-fixed,
            .admin-sidebar-fixed.col-auto,
            .admin-sidebar-fixed.col-md-3,
            .admin-sidebar-fixed.col-xl-2 {
                position: static !important;
                width: 100% !important;
                max-width: 100%;
                transform: none !important;
                z-index: 1040;
                box-shadow: inset 0px -4px 6px -4px rgba(0,0,0,0.5) !important;

                max-height: 0;
                overflow: hidden !important;
                transition: max-height 0.4s ease-in-out !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }

            .admin-sidebar-fixed.show {
                max-height: 800px; /* Suficiente para mostrar los enlaces */
                overflow-y: auto !important;
            }

            /* Quitar el min-viewport height para que se acople al contenido o esconda en 0 */
            .admin-sidebar-fixed .min-vh-100 {
                min-height: 0 !important;
                height: auto !important;
            }

            .admin-content {
                margin-left: 0 !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                width: 100%;
            }
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
        @if (session('success') || session('message') || session('status'))
            <div id="global-notification" class="animated fadeInDown" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #28a745 0%, #218838 100%);
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 600;
                border: 1px solid rgba(255,255,255,0.1);
                backdrop-filter: blur(8px);
                transition: all 0.5s ease;
            ">
                <i class="fas fa-check-circle fs-4"></i>
                <span>{{ session('success') ?? session('message') ?? session('status') }}</span>
                <button type="button" class="btn-close btn-close-white ms-2" onclick="this.parentElement.remove()" style="font-size: 0.8rem;"></button>
            </div>
            <script src="{{ asset('js/notificaciones.js') }}"></script>
        @endif

    <!-- Admin Panel -->
    @if(isset($adminView))
        <!-- Mobile Topbar Navbar (Only visible on mobile) -->
        <div class="d-md-none bg-dark d-flex justify-content-between align-items-center p-3 text-white shadow-sm sticky-top">
            <h5 class="m-0 text-warning d-flex align-items-center">
                <i class="bi bi-shield-lock me-2"></i> Admin Panel
            </h5>
            <button id="mobile-sidebar-toggle" class="btn btn-outline-warning btn-sm border-0">
                <i class="bi bi-list fs-2"></i>
            </button>
        </div>

        <div class="container-fluid">
            <div class="row flex-nowrap">
                @if(View::hasSection('sidebar'))
                    @yield('sidebar')
                @elseif(isset($sidebarView))
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

