@extends('layouts.app')

@section('header')
    @include('layouts.header')
    <!-- Page Header Start -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url(img/carousel-1.jpg);">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Rooms</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Paginas</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Reservas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Mis Reservas</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Habitación</th>
                    <th scope="col">Check in</th>
                    <th scope="col">Check out</th>
                    <th scope="col">Total</th>
                    <th scope="col">Reservado</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->room->roomtype->name }}</td>
                        <td>{{ $order->check_in->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}</td>
                        <td>{{ $order->check_out->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}</td>
                        <td>@cop($order->room->price, $order->stayDays)</td>
                        <td>{{ $order->created_at->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}</td>
                    </tr>
                @empty
                    <p class="text-primary fw-bold">No tienes reservas aún.</p>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Newsletter -->
    @include('sections.newsletter')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection
