<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Nuestras Habitaciones</h6>
            <h1 class="mb-5">Explora nuestras <span class="text-primary text-uppercase">Habitaciones</span></h1>
        </div>
        <div class="row g-4">
            @foreach($rooms as $room)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $loop->iteration/10 }}s">
                    <div class="room-item shadow rounded overflow-hidden">
                        <div class="position-relative image-container">
                            <img src="{{ asset($room->image) }}" alt="">
                            <small class="position-absolute start-0 top-0 bg-primary text-white rounded py-1 px-3 ms-4">
                                @cop($room->price)/Noche
                            </small>
                        </div>
                        <div class="p-4 mt-2">
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="mb-0">{{ $room->roomtype->name }}</h5>
                                <div class="ps-2">
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i>
                                    {{ $room->no_beds }}Camas </small>
                                <small class="border-end me-3 pe-3"><i
                                        class="fa fa-bath text-primary me-2"></i>Baño</small>
                                <small><i class="fa fa-wifi text-primary me-2"></i>Wifi</small>
                            </div>
                            <p class="text-body mb-3">{{ $room->desc }}</p>
                            <div class="d-flex">
                                @if(isset($searched))
                                    <button type="button"
                                            class="btn btn-sm btn-success rounded py-2 px-4"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reservationModal"
                                            data-reserve-room-id="{{ $room->id }}">
                                        Reserva
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationModalLabel">Completar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('orders.store') }}">
                @csrf
                <div class="modal-body">
                    <p>Para procesar tu reserva, por favor ingresa tu correo electrónico. Te enviaremos los detalles para el pago del anticipo (30%).</p>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" id="email" required placeholder="ejemplo@correo.com">
                    </div>

                    <input type="hidden" name="room_id" id="modal_room_id">
                    <input type="hidden" name="check_in" value="{{ $fields['check_in'] ?? '' }}">
                    <input type="hidden" name="check_out" value="{{ $fields['check_out'] ?? '' }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/blade/sections/room-container-details--script1.js') }}"></script>
