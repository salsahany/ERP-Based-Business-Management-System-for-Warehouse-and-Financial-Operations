@extends('layouts.dashboard')

@section('title', 'Tambah Pengeluaran Kas Kecil')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Tambah Pengeluaran Kas Kecil</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('petty-cash.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengeluaran</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nominal (Rp)</label>
                    <input type="text" name="nominal" class="form-control" id="nominalInput" placeholder="0" value="{{ old('nominal') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bukti Nota (opsional)</label>
                    <input type="file" name="bukti_nota" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <small class="text-muted">JPG, PNG, atau PDF. Maks 2MB.</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi pengeluaran (opsional)">{{ old('keterangan') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('petty-cash.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('nominalInput');
    input.addEventListener('input', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) {
            this.value = new Intl.NumberFormat('id-ID').format(val);
        }
    });
});
</script>

@endsection
