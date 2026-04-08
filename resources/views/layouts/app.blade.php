<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon-hotel.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tu archivo app.css y JS -->
    @stack('styles')
    @stack('head')

    <link rel="stylesheet" href="{{ asset('css/blade/layouts/app--style1.css') }}">

    <title>Hotel Oasis Colina</title>
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
                <button type="button" class="btn-close btn-close-white ms-2" data-dismiss-parent="#global-notification" style="font-size: 0.8rem;"></button>
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
    <script src="{{ asset('js/ui-events.js') }}"></script>
    @stack('scripts')
</body>
</html>



