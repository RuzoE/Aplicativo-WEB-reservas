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
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->roomType->name ?? ($order->room->roomtype->name ?? 'N/A') }}</td>
                        <td>{{ $order->check_in->format('d/m/Y') }}</td>
                        <td>{{ $order->check_out->format('d/m/Y') }}</td>
                        <td>@cop($order->room->price ?? ($order->roomType->rooms->first()->price ?? 0), $order->stayDays)</td>
                        <td>{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                        <td>
                            @if($order->status == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente Pago</span>
                            @elseif($order->status == 'confirmada')
                                <span class="badge bg-success">Confirmada</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(!in_array($order->status, ['finalizada', 'cancelada']))
                                <a href="{{ route('orders.edit', ['user_order' => $order->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-edit me-1"></i> Modificar
                                </a>
                            @else
                                <span class="text-muted small">Sin acciones</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <p class="text-primary fw-bold">No tienes reservas aún.</p>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Newsletter -->
    @include('components.sections.newsletter')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection
