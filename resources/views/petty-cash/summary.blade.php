@extends('layouts.dashboard')

@section('title', 'Ringkasan Petty Cash')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Ringkasan Pengeluaran Kas Kecil</h4>
    <a href="{{ route('petty-cash.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

{{-- Date Filter --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('petty-cash.summary') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('petty-cash.summary') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Table --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-end">Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $row)
                <tr>
                    <td><span class="badge bg-secondary">{{ $row->kategori }}</span></td>
                    <td class="text-center">{{ $row->jumlah }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted p-4">Belum ada data pengeluaran yang disetujui.</td>
                </tr>
                @endforelse
            </tbody>
            @if($summary->count())
            <tfoot>
                <tr class="table-secondary">
                    <td class="fw-bold">Grand Total</td>
                    <td class="text-center fw-bold">{{ $summary->sum('jumlah') }}</td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<small class="text-muted mt-2 d-block">* Hanya menampilkan pengeluaran dengan status <strong>Approved</strong>.</small>

@endsection
