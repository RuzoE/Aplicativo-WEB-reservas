@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input/build/css/intlTelInput.css">
    <link rel="stylesheet" href="{{ asset('css/blade/auth/register--style1.css') }}">
@endpush

@section('header')
    @include('layouts.header')
@endsection

@section('content')
    <div class="container-fluid mt-5 auth-container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title my-4 text-center">Crear Cuenta</h4>
                        <form novalidate class="row g-3" method="post" action="{{ route('register') }}">
                            @csrf
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label auth-phone-label">Nombre</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                    <input id="name" type="text" placeholder="Ej: Fabian" name="name" value="{{ old('name') }}" required
                                           class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_name" class="form-label auth-phone-label">Apellidos</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                    <input id="last_name" type="text" placeholder="Ej: Rojas" name="last_name" value="{{ old('last_name') }}" required
                                           class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="phone" class="form-label auth-phone-label">Número de teléfono</label>
                                <input type="hidden" name="phone_country" id="phone_country" value="{{ old('phone_country', 'co') }}">

                                <div class="auth-phone-wrapper">
                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="Ej: 3000000000"
                                        required
                                        inputmode="numeric"
                                        autocomplete="tel"
                                        data-phone-input="true"
                                        class="form-control auth-phone-input @error('phone') is-invalid @enderror"
                                    >
                                </div>

                                <div id="phoneFeedback" class="invalid-feedback @error('phone') d-block @enderror">@error('phone'){{ $message }}@enderror</div>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label auth-phone-label">Correo electrónico</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                                    <input id="email" type="email" placeholder="Ej: fabianrojas@gmail.com" name="email"
                                           value="{{ old('email') }}" required
                                           pattern="^[^\s@]+@(gmail\.com|hotmail\.com)$"
                                           title="Solo se permiten correos @gmail.com o @hotmail.com"
                                           class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="password" class="form-label auth-phone-label">Contraseña</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                    <input id="password" type="password" placeholder="Ej: MiClaveSegura@2026" name="password"
                                                                                     minlength="12" required
                                                                                     title="Mínimo 12 caracteres con mayúscula, minúscula, número y símbolo"
                                           class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="password_confirmation" class="form-label auth-phone-label">Confirmar contraseña</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                    <input id="password_confirmation" type="password" placeholder="Repite tu contraseña" name="password_confirmation"
                                                                                     minlength="12" required
                                           class="form-control @error('password_confirmation') is-invalid @enderror">
                                    @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Crear Cuenta</button>
                            </div>
                            <p class="text-center">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión Ahora</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input/build/js/intlTelInput.min.js"></script>
    <script>
        window.phoneCountryConfig = {!! json_encode(config('phone.country_lengths') ?? []) !!};
    </script>
    <script src="{{ asset('js/blade/auth/register--script1.js') }}"></script>
@endpush
