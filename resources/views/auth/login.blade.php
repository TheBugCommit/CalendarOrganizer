@extends('layouts.main')

@section('title', 'CO - Login')

@section('content')
    <div id="auth-card">
        <div class="container">
            <div class="card login-card">
                <div class="row no-gutters">
                    <div class="col-md-5">
                        <img src="/img/login.jpg" alt="login" class="login-card-img">
                    </div>
                    <div class="col-md-7">
                        <div class="card-body">
                            <div class="brand-wrapper title">
                                <img src="/img/logo.webp" alt="logo" class="logo">
                                Calendar Organizer
                            </div>
                            <p class="login-card-description">Sign into your account</p>
                            <form action="{{ route('auth.authenticate') }}" id="auth-form" method="post">
                                @csrf
                                <div class="input_group field">
                                    <input type="text" class="input_field validate @error('email') invalid @enderror"
                                        placeholder="Email" value="{{ old('email') }}" name="email" id='email' required />
                                    <label for="email" class="input_label">Email</label>
                                    <span class="input-error d-none caps-lock">Caps Lock activated</span>
                                    @error('email')
                                        <span class="input-error d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input_group field">
                                    <input type="password" class="input_field validate @error('password') invalid @enderror"
                                        placeholder="Password" name="password" id='password' required />
                                    <label for="password" class="input_label">Password</label>
                                    <span class="input-error d-none caps-lock">Caps Lock activated</span>
                                    @error('password')
                                        <span class="input-error d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-flex w-100 justify-content-between">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" id="terms">
                                        <label for="terms">Accept terms & conditions</label>
                                    </div>
                                    <input id="login" class="btn btn-default mt-4 mb-4 btn-opacity-0" disabled type="submit"
                                        value="Login">
                                </div>
                            </form>
                            <p class="text">Don't have an account? <a class="login-link" href="{{ route('auth.signup') }}">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
