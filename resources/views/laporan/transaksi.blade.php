@extends('layouts.dashboard')

@section('title', 'Laporan Transaksi')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Laporan Transaksi</h4>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date"
                       value="{{ request('start_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date"
                       value="{{ request('end_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-3 align-self-end">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('laporan.transaksi') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['tanggal'] }}</td>
                        <td>{{ $item['produk'] }}</td>
                        <td>
                            @if($item['jenis'] === 'Masuk')
                                <span class="badge bg-success">Masuk</span>
                            @else
                                <span class="badge bg-danger">Keluar</span>
                            @endif
                        </td>
                        <td>{{ $item['jumlah'] }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Tidak ada data transaksi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
