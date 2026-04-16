@extends('layouts.dashboard')

@section('title', 'Barang Keluar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Data Barang Keluar</h4>

    <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary">
        + Tambah Barang Keluar
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
                    <th>Tipe</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangKeluar as $item)
                <tr>
                    <td>{{ $item->tanggal_keluar }}</td>
                    <td>{{ $item->product->nama_produk }}</td>
                    <td>
                        @if($item->product->satuan == 'Rp')
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        @else
                            {{ $item->jumlah }} {{ $item->product->satuan }}
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $item->tipe == 'logbook' ? 'bg-warning text-dark' : 'bg-primary' }}">
                            {{ ucfirst($item->tipe ?? 'project') }}
                        </span>
                    </td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Data barang keluar belum ada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
