@extends('layouts.app')

@section('header')
    @include('layouts.header')
    <!-- Page Header Start -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url(img/carousel-1.jpg);">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Habitaciones</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Paginas</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Habitaciones</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
    <!-- Booking -->
    @include('sections.booking-header')
@endsection

@section('content')
    <!-- Room -->
    @include('sections.room-container-details')
    <!-- Testimonial -->
    @include('sections.testimonial')
    <!-- Newsletter -->
    @include('sections.newsletter')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection


@section('footer')
    @include('layouts.footer')
@endsection
