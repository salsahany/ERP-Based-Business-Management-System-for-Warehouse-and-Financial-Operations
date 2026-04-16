@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <div class="card auth-card p-4">
            <h4 class="text-center mb-4 fw-bold">Login</h4>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">
                    Login
                </button>
            </form>

            <p class="text-center mt-3 text-muted">
                Belum punya akun?
                <a href="{{ route('register') }}">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection
