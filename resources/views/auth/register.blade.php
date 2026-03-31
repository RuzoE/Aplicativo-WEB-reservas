@extends('layouts.app')

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
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                    <input type="text" placeholder="Nombre" name="name" value="{{ old('name') }}" required
                                           class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                    <input type="text" placeholder="Apellidos" name="last_name" value="{{ old('last_name') }}" required
                                           class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
                                    <input type="tel" placeholder="Número de teléfono" name="phone" value="{{ old('phone') }}" required
                                           inputmode="tel"
                                         minlength="10"
                                         maxlength="16"
                                                                                 data-phone-sanitize="true"
                                           pattern="^(3\d{9}|(?:\+57|57)3\d{9}|\+\d{8,15}|\d{8,15})$"
                                           title="Si inicia en 3 debe tener 10 dígitos (Colombia). También se acepta formato internacional válido."
                                           class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                                    <input type="email" placeholder="Email" name="email"
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
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                    <input type="password" placeholder="Ingresa Contraseña" name="password"
                                                                                     minlength="12" required
                                                                                     title="Mínimo 12 caracteres con mayúscula, minúscula, número y símbolo"
                                           class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group has-validation">
                                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                    <input type="password" placeholder="Repite la contraseña" name="password_confirmation"
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

