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
                                    <form method="post" action="{{ route('orders.store') }}">
                                        @csrf
                                        <div class="d-none">
                                            <input type="date" name="check_in" value="{{ $fields['check_in'] }}">
                                            <input type="text" name="check_out" value="{{ $fields['check_out'] }}">
                                            <input type="number" name="room_id" value="{{ $room->id }}">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success rounded py-2 px-4">Reserva
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
