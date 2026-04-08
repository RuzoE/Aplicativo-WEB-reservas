@extends('layouts.app')

@section('header')
    @include('layouts.header')
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-1.jpg') }});">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Modificar Reserva</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Mis Reservas</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Modificar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-4">
                    <h3 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Ajustar Fechas de Estancia</h3>
                    <p class="mb-0 opacity-75">Mantendremos la misma duración de <strong>{{ $duration }}</strong> {{ $duration == 1 ? 'noche' : 'noches' }}.</p>
                </div>
                <div class="card-body p-5">
                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- Vue Component Root -->
                    <div id="reservation-date-modifier-root"
                        data-order-id="{{ $order->id }}"
                        data-current-check-in="{{ $order->check_in->format('Y-m-d') }}"
                        data-duration="{{ $duration }}"
                        data-update-url="{{ route('orders.update', ['user_order' => $order->id]) }}"
                        data-csrf-token="{{ csrf_token() }}"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/app.css'])
@endpush

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush

@section('footer')
    @include('layouts.footer')
@endsection
