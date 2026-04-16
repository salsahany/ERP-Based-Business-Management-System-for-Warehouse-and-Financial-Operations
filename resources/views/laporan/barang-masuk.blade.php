@extends('layouts.dashboard')

@section('title', 'Laporan Barang Masuk')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Laporan Barang Masuk</h4>
</div>

{{-- FILTER TANGGAL --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">

        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date"
                       name="start_date"
                       value="{{ request('start_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date"
                       name="end_date"
                       value="{{ request('end_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-4">
                <button class="btn btn-primary me-2">
                    Filter
                </button>

                <a href="{{ route('laporan.barang-masuk') }}"
                   class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>

    </div>
</div>

{{-- TABEL LAPORAN --}}
<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th width="120">Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}</td>
                    <td>{{ $item->product->nama_produk }}</td>
                    <td class="fw-bold">{{ $item->jumlah }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Tidak ada data barang masuk
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
