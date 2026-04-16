@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <div class="card auth-card p-4">
            <h4 class="text-center mb-4 fw-bold">Register</h4>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">
                    Register
                </button>
            </form>

            <p class="text-center mt-3 text-muted">
                Sudah punya akun?
                <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
