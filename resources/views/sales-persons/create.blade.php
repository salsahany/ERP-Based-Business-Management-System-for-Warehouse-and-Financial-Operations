@extends('layouts.dashboard')

@section('title', 'Tambah Sales')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Tambah Sales</h4>
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

        <form action="{{ route('sales-persons.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Sales</label>
                <input type="text"
                       name="nama"
                       value="{{ old('nama') }}"
                       class="form-control"
                       required
                       placeholder="Contoh: Rahmat">
            </div>

            <div class="mb-3">
                <label class="form-label">No HP</label>
                <input type="text"
                       name="no_hp"
                       value="{{ old('no_hp') }}"
                       class="form-control"
                       placeholder="Contoh: 08123456789">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('sales-persons.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </form>

    </div>
</div>

@endsection
