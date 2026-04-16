@extends('layouts.dashboard')

@section('title', 'Laporan Stok Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Laporan Stok Barang</h4>
    <div class="d-flex align-items-center gap-3">
        <div class="bg-white px-3 py-2 rounded shadow-sm border">
            <small class="text-muted d-block" style="font-size: 0.8rem;">Total Nilai Aset</small>
            <span class="fw-bold text-success">Rp {{ number_format($totalAsset, 0, ',', '.') }}</span>
        </div>
        <button onclick="window.print()" class="btn btn-primary d-print-none">
            🖨️ Print Laporan
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Satuan</th>
                    <th width="120">Stok Tersedia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->kode_produk }}</td>
                        <td>{{ $product->nama_produk }}</td>
                        <td>{{ $product->satuan }}</td>
                        <td class="fw-bold">
                            {{ $product->stok }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada data produk
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .navbar, .btn, .d-print-none {
            display: none !important;
        }
        .content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>
@endpush
