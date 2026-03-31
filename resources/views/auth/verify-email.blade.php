@extends('layouts.app')

@section('header')
    @include('layouts.header')
@endsection

@section('content')
    <div class="container mt-5 mb-5" style="max-width: 720px;">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h3 class="mb-3">Verifica tu correo electrónico</h3>
                <p class="text-muted mb-4">
                    Para continuar, debes verificar tu correo. Revisa tu bandeja de entrada y haz clic en el enlace de verificación.
                </p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Reenviar enlace de verificación</button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-2">Volver al inicio</a>
                </form>
            </div>
        </div>
    </div>
@endsection
