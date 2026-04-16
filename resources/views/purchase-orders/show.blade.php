@extends('layouts.dashboard')

@section('title', 'Detail PO - ' . $po->no_po)

@section('content')

<div class="mb-4">
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2">← Kembali</a>
    <h4 class="fw-bold">Detail Purchase Order</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- PO Header Info --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th style="width:150px">No PO</th><td>: <strong>{{ $po->no_po }}</strong></td></tr>
                    <tr><th>Admin</th><td>: {{ $po->admin->name }}</td></tr>
                    <tr><th>Tipe PO</th><td>: 
                        <span class="badge {{ $po->tipe_po == 'saldo' ? 'bg-warning text-dark' : 'bg-primary' }}">
                            {{ ucfirst($po->tipe_po) }}
                        </span>
                    </td></tr>
                    <tr><th>Status</th><td>: 
                        <span class="badge {{ $po->statusBadge['class'] }}">
                            {{ $po->statusBadge['label'] }}
                        </span>
                    </td></tr>
                    <tr><th>Tanggal Dibuat</th><td>: {{ $po->created_at->format('d/m/Y H:i') }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th style="width:180px">Total Pengajuan</th><td>: <strong>Rp {{ number_format($po->total_pengajuan, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Total Disetujui</th><td>: 
                        @if($po->status === 'approved')
                            <strong class="text-success">Rp {{ number_format($po->total_disetujui, 0, ',', '.') }}</strong>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td></tr>
                    @if($po->catatan_admin)
                    <tr><th>Catatan Admin</th><td>: {{ $po->catatan_admin }}</td></tr>
                    @endif
                    @if($po->catatan_finance)
                    <tr><th>Catatan Finance</th><td>: {{ $po->catatan_finance }}</td></tr>
                    @endif
                    @if($po->catatan_owner)
                    <tr><th>Catatan Owner</th><td>: {{ $po->catatan_owner }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Items Table --}}
<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0">Item PO</h5>
    </div>
    <div class="card-body p-0">

        {{-- Finance Finalize Form wraps the table --}}
        @if(auth()->user()->role === 'finance' && $po->status === 'waiting_final_adjustment')
        <form method="POST" action="{{ route('purchase-orders.finalize', $po->id) }}">
            @csrf
        @endif

        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty Pengajuan</th>
                    @if($po->status === 'waiting_final_adjustment' && auth()->user()->role === 'finance')
                        <th class="text-center" style="width: 150px;">Qty Disetujui</th>
                    @elseif(in_array($po->status, ['approved']))
                        <th class="text-center">Qty Disetujui</th>
                    @endif
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">{{ number_format($item->qty_pengajuan, 0, ',', '.') }}</td>
                    
                    @if($po->status === 'waiting_final_adjustment' && auth()->user()->role === 'finance')
                        <td class="text-center">
                            <input type="number" 
                                   name="qty_disetujui[{{ $item->id }}]" 
                                   class="form-control form-control-sm text-center" 
                                   value="{{ $item->qty_disetujui ?? $item->qty_pengajuan }}" 
                                   min="0" 
                                   required>
                        </td>
                    @elseif($po->status === 'approved')
                        <td class="text-center">
                            @if($item->qty_disetujui != $item->qty_pengajuan)
                                <span class="text-danger fw-bold">{{ number_format($item->qty_disetujui, 0, ',', '.') }}</span>
                                <br><small class="text-muted">(diajukan: {{ number_format($item->qty_pengajuan, 0, ',', '.') }})</small>
                            @else
                                <span class="text-success">{{ number_format($item->qty_disetujui, 0, ',', '.') }}</span>
                            @endif
                        </td>
                    @endif

                    <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td colspan="{{ in_array($po->status, ['waiting_final_adjustment', 'approved']) ? 5 : 4 }}" class="text-end fw-bold">Total:</td>
                    <td class="text-end fw-bold">Rp {{ number_format($po->total_pengajuan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        @if(auth()->user()->role === 'finance' && $po->status === 'waiting_final_adjustment')
            <div class="p-3 text-end">
                <button type="submit" class="btn btn-success" onclick="return confirm('Finalisasi PO ini? Total disetujui akan dihitung otomatis.')">
                    Finalisasi & Setujui PO
                </button>
            </div>
        </form>
        @endif

    </div>
</div>

{{-- Action Buttons per Role & Status --}}
@php $user = auth()->user(); @endphp

{{-- Finance: Approve (pending_finance → pending_owner) --}}
@if($user->role === 'finance' && $po->status === 'pending_finance')
<div class="card shadow-sm mb-3 border-primary">
    <div class="card-header bg-primary text-white">Aksi Finance: Validasi PO</div>
    <div class="card-body">
        <form method="POST" action="{{ route('purchase-orders.approve-finance', $po->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Catatan Finance (opsional)</label>
                <textarea name="catatan_finance" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Teruskan PO ini ke Owner?')">
                Teruskan ke Owner
            </button>
        </form>
    </div>
</div>
@endif

{{-- Owner: Approve/Reject (pending_owner → waiting_final_adjustment / rejected) --}}
@if($user->role === 'owner' && $po->status === 'pending_owner')
<div class="card shadow-sm mb-3 border-success">
    <div class="card-header bg-success text-white">Aksi Owner: Persetujuan PO</div>
    <div class="card-body">
        <form method="POST" action="{{ route('purchase-orders.approve-owner', $po->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Catatan Owner (opsional)</label>
                <textarea name="catatan_owner" class="form-control" rows="2"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="approve" class="btn btn-success" onclick="return confirm('Setujui PO ini?')">
                    Setujui
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Tolak PO ini?')">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Approved: Show comparison for Admin --}}
@if($po->status === 'approved' && $user->role === 'admin')
<div class="card shadow-sm mb-3 border-success">
    <div class="card-header bg-success text-white">PO Disetujui</div>
    <div class="card-body">
        <p class="mb-1"><strong>Total Pengajuan:</strong> Rp {{ number_format($po->total_pengajuan, 0, ',', '.') }}</p>
        <p class="mb-1"><strong>Total Disetujui:</strong> Rp {{ number_format($po->total_disetujui, 0, ',', '.') }}</p>
        @if($po->total_disetujui < $po->total_pengajuan)
            <p class="text-warning mb-0">
                <small>Selisih: Rp {{ number_format($po->total_pengajuan - $po->total_disetujui, 0, ',', '.') }} (dikurangi oleh Finance)</small>
            </p>
        @endif
        <p class="text-muted mt-2 mb-0"><small>Difinalisasi pada: {{ $po->finalized_at?->format('d/m/Y H:i') }}</small></p>
    </div>
</div>
@endif

@endsection
