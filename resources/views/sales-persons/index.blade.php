@extends('layouts.dashboard')

@section('title', 'Master Sales')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Master Sales</h4>

    <a href="{{ route('sales-persons.create') }}" class="btn btn-primary">
        + Tambah Sales
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesPersons as $sales)
                <tr>
                    <td>{{ $sales->nama }}</td>
                    <td>{{ $sales->no_hp ?? '-' }}</td>
                    <td>
                        <a href="{{ route('sales-persons.edit', $sales) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <form action="{{ route('sales-persons.destroy', $sales) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data sales ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Data sales belum ada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
