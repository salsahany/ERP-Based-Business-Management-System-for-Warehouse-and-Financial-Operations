@extends('layouts.dashboard')

@section('title', 'Edit Sales')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Edit Sales</h4>
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

        <form action="{{ route('sales-persons.update', $salesPerson) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Sales</label>
                <input type="text"
                       name="nama"
                       value="{{ old('nama', $salesPerson->nama) }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">No HP</label>
                <input type="text"
                       name="no_hp"
                       value="{{ old('no_hp', $salesPerson->no_hp) }}"
                       class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Update
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
