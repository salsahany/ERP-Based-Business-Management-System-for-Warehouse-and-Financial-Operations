@extends('layouts.dashboard')

@section('title', 'Purchase Orders')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Purchase Orders</h4>

    @if(auth()->user()->role === 'admin')
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
        + Buat PO Baru
    </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No PO</th>
                        <th>Tipe</th>
                        <th>Admin</th>
                        <th>Total Pengajuan</th>
                        <th>Total Disetujui</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                    <tr>
                        <td class="fw-bold">{{ $po->no_po }}</td>
                        <td>
                            <span class="badge {{ $po->tipe_po == 'saldo' ? 'bg-warning text-dark' : 'bg-primary' }}">
                                {{ ucfirst($po->tipe_po) }}
                            </span>
                        </td>
                        <td>{{ $po->admin->name }}</td>
                        <td class="text-end">Rp {{ number_format($po->total_pengajuan, 0, ',', '.') }}</td>
                        <td class="text-end">
                            @if($po->status === 'approved')
                                Rp {{ number_format($po->total_disetujui, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $po->statusBadge['class'] }}">
                                {{ $po->statusBadge['label'] }}
                            </span>
                        </td>
                        <td>{{ $po->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted p-4">
                            Belum ada Purchase Order.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
