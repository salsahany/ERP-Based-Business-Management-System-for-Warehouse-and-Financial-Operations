@extends('layouts.dashboard')

@section('title', 'Manajemen Wilayah')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Manajemen Wilayah</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Add Wilayah Form --}}
<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white">Tambah Wilayah Baru</div>
    <div class="card-body">
        <form method="POST" action="{{ route('wilayah.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Nama Wilayah</label>
                <input type="text" name="nama_wilayah" class="form-control" placeholder="Contoh: Surabaya" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>
        @error('nama_wilayah')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

{{-- List Wilayahs with Admin Assignment --}}
@forelse($wilayahs as $wilayah)
<div class="card shadow-sm mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $wilayah->nama_wilayah }}</h5>
        <form method="POST" action="{{ route('wilayah.destroy', $wilayah->id) }}" class="d-inline" onsubmit="return confirm('Hapus wilayah {{ $wilayah->nama_wilayah }}? Data terkait akan kehilangan wilayah.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
        </form>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('wilayah.assign-admins', $wilayah->id) }}">
            @csrf
            <label class="form-label fw-bold">Admin yang ditugaskan:</label>
            <div class="row">
                @foreach($admins as $admin)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               name="admin_ids[]" 
                               value="{{ $admin->id }}" 
                               id="admin_{{ $wilayah->id }}_{{ $admin->id }}"
                               {{ $wilayah->users->contains($admin->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="admin_{{ $wilayah->id }}_{{ $admin->id }}">
                            {{ $admin->name }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @if($admins->isEmpty())
                <p class="text-muted">Belum ada user dengan role Admin.</p>
            @else
                <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Simpan Penugasan</button>
            @endif
        </form>
    </div>
</div>
@empty
<div class="card shadow-sm">
    <div class="card-body text-center text-muted p-4">
        <i class="bi bi-geo-alt fs-4 d-block mb-2"></i>
        Belum ada wilayah. Silakan tambah wilayah baru di atas.
    </div>
</div>
@endforelse

@endsection
