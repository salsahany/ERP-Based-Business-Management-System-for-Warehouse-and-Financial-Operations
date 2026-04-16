@extends('layouts.dashboard')

@section('title', 'Petty Cash')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Petty Cash (Kas Kecil)</h4>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['finance', 'owner']))
        <a href="{{ route('petty-cash.summary') }}" class="btn btn-outline-info">
            📊 Ringkasan
        </a>
        @endif
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('petty-cash.create') }}" class="btn btn-primary">
            + Tambah Pengeluaran
        </a>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Status Filter --}}
<div class="mb-3">
    <div class="btn-group btn-group-sm">
        <a href="{{ route('petty-cash.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
        <a href="{{ route('petty-cash.index', ['status' => 'pending']) }}" class="btn {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('petty-cash.index', ['status' => 'approved']) }}" class="btn {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">Approved</a>
        <a href="{{ route('petty-cash.index', ['status' => 'rejected']) }}" class="btn {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Admin</th>
                        <th class="text-end">Nominal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pettyCash as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                        <td>{{ Str::limit($item->keterangan, 40) ?: '-' }}</td>
                        <td>{{ $item->admin->name }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $item->statusBadge['class'] }}">
                                {{ $item->statusBadge['label'] }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('petty-cash.show', $item->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted p-4">Belum ada data petty cash.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($pettyCash->count())
                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td class="text-end fw-bold">Rp {{ number_format($pettyCash->sum('nominal'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection
