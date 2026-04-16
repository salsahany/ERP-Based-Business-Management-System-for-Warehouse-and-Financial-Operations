@extends('layouts.dashboard')

@section('title', 'Edit Produk')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Edit Produk</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        {{-- Alert error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Kode Produk</label>
                <input type="text"
                       name="kode_produk"
                       value="{{ old('kode_produk', $product->kode_produk) }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text"
                       name="nama_produk"
                       value="{{ old('nama_produk', $product->nama_produk) }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Satuan</label>
                <input type="text"
                       name="satuan"
                       value="{{ old('satuan', $product->satuan) }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number"
                       name="harga"
                       value="{{ old('harga', $product->harga) }}"
                       class="form-control"
                       min="0"
                       required>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Update
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
