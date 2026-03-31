@extends('layouts.app')

@section('header')
    @include('layouts.header')
    <!-- Carousel -->
    @include('components.sections.carousel')
@endsection

@section('content')
    <!-- Service -->
    @include('components.sections.service')
    <!-- Room -->
    @include('components.sections.room-container-brief')
    <!-- Testimonial -->
    @include('components.sections.testimonial')
    <!-- Team -->
    @include('components.sections.team')
    <!-- Newsletter -->
    @include('components.sections.newsletter')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection


