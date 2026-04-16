@extends('layouts.dashboard')

@section('title', 'Barang Masuk')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Data Barang Masuk</h4>

    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
        + Tambah Barang Masuk
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangMasuk as $item)
                <tr>
                    <td>{{ $item->tanggal_masuk }}</td>
                    <td>{{ $item->product->nama_produk }}</td>
                    <td>
                        @if($item->product->satuan == 'Rp')
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        @else
                            {{ $item->jumlah }} {{ $item->product->satuan }}
                        @endif
                    </td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Data barang masuk belum ada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
