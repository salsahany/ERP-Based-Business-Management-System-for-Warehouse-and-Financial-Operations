@extends('layouts.app')

@section('title', 'Warehouse App')

@section('content')
<div class="container vh-100 d-flex align-items-center">
    <div class="row w-100 align-items-center">
        <div class="col-md-6">
            <h1 class="fw-bold mb-3">Warehouse Management System</h1>
            <p class="text-muted mb-4">
                Aplikasi manajemen stok barang masuk dan keluar berbasis web.
            </p>

            <a href="{{ route('login') }}" class="btn btn-primary me-2">
                Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                Register
            </a>
        </div>

        <div class="col-md-6 text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/869/869636.png"
                 class="img-fluid"
                 style="max-height: 300px;">
        </div>
    </div>
</div>
@endsection
