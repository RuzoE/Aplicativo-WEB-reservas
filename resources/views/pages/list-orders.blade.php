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
                            @if($order->status == 'pendiente_pago' || $order->status == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente Pago</span>
                            @elseif($order->status == 'reserva_previa')
                                <span class="badge bg-secondary text-white">Pago Realizado</span>
                            @elseif($order->status == 'reserva_asignada')
                                <span class="badge bg-success text-white">Reserva Asignada</span>
                            @elseif($order->status == 'confirmada')
                                <span class="badge bg-success">Confirmada</span>
                            @else
                                <span class="badge bg-dark text-white">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(!in_array($order->status, ['finalizada', 'cancelada']))
                                <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
                                    <a href="{{ route('orders.edit', ['user_order' => $order->id]) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-edit"></i>
                                        <span>Modificar</span>
                                    </a>

                                    <form action="{{ route('orders.destroy', ['user_order' => $order->id]) }}"
                                          method="POST"
                                          class="delete-order-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2 w-100">
                                            <i class="fas fa-trash-alt"></i>
                                            <span>Eliminar</span>
                                        </button>
                                    </form>
                                </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-order-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar esta reserva?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush

@section('footer')
    @include('layouts.footer')
@endsection
