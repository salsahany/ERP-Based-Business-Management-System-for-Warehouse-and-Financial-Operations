@extends('layouts.dashboard')

@section('title', 'Tambah Produk')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Tambah Produk</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Kode Produk</label>
                <input type="text"
                       name="kode_produk"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text"
                       name="nama_produk"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Satuan</label>
                <input type="text"
                       name="satuan"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number"
                       name="harga"
                       class="form-control"
                       min="0"
                       value="0"
                       required>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('products.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
