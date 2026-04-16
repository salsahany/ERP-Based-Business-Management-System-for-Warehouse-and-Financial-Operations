@extends('layouts.dashboard')

@section('title', 'Detail Petty Cash')

@section('content')

<div class="mb-4">
    <a href="{{ route('petty-cash.index') }}" class="btn btn-sm btn-outline-secondary mb-2">← Kembali</a>
    <h4 class="fw-bold">Detail Pengeluaran Kas Kecil</h4>
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

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th style="width:150px">Tanggal</th><td>: {{ $pettyCash->tanggal->format('d/m/Y') }}</td></tr>
                    <tr><th>Kategori</th><td>: <span class="badge bg-secondary">{{ $pettyCash->kategori }}</span></td></tr>
                    <tr><th>Nominal</th><td>: <strong>Rp {{ number_format($pettyCash->nominal, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Admin</th><td>: {{ $pettyCash->admin->name }}</td></tr>
                    @if($pettyCash->wilayah)
                    <tr><th>Wilayah</th><td>: {{ $pettyCash->wilayah->nama_wilayah }}</td></tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th style="width:150px">Status</th><td>: 
                        <span class="badge {{ $pettyCash->statusBadge['class'] }}">
                            {{ $pettyCash->statusBadge['label'] }}
                        </span>
                    </td></tr>
                    @if($pettyCash->keterangan)
                    <tr><th>Keterangan</th><td>: {{ $pettyCash->keterangan }}</td></tr>
                    @endif
                    @if($pettyCash->catatan_finance)
                    <tr><th>Catatan Finance</th><td>: {{ $pettyCash->catatan_finance }}</td></tr>
                    @endif
                    @if($pettyCash->approver)
                    <tr><th>Diproses oleh</th><td>: {{ $pettyCash->approver->name }}</td></tr>
                    <tr><th>Waktu</th><td>: {{ $pettyCash->approved_at->format('d/m/Y H:i') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Bukti Nota --}}
@if($pettyCash->bukti_nota)
<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0">Bukti Nota</h5>
    </div>
    <div class="card-body text-center">
        @if(Str::endsWith($pettyCash->bukti_nota, ['.jpg', '.jpeg', '.png']))
            <img src="{{ asset($pettyCash->bukti_nota) }}" alt="Bukti Nota" class="img-fluid rounded" style="max-height: 400px;">
        @else
            <a href="{{ asset($pettyCash->bukti_nota) }}" target="_blank" class="btn btn-outline-primary">
                📄 Lihat PDF
            </a>
        @endif
    </div>
</div>
@endif

{{-- Finance Approval Panel --}}
@if(auth()->user()->role === 'finance' && $pettyCash->status === 'pending')
<div class="card shadow-sm mb-3 border-primary">
    <div class="card-header bg-primary text-white">Aksi Finance: Review Pengeluaran</div>
    <div class="card-body">
        <form method="POST" action="{{ route('petty-cash.approve', $pettyCash->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Catatan Finance (opsional)</label>
                <textarea name="catatan_finance" class="form-control" rows="2"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="approve" class="btn btn-success" onclick="return confirm('Setujui pengeluaran ini?')">
                    ✅ Approve
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Tolak pengeluaran ini?')">
                    ❌ Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
