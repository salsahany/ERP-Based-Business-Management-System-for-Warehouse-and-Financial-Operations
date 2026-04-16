@extends('layouts.dashboard')

@section('title', 'Master Produk')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Master Produk</h4>

    <a href="{{ route('products.create') }}" class="btn btn-primary">
        + Tambah Produk
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>{{ $p->kode_produk }}</td>
                    <td>{{ $p->nama_produk }}</td>
                    <td>{{ $p->satuan }}</td>
                    <td>
                        <div id="price-display-{{ $p->id }}" class="d-flex justify-content-between align-items-center">
                            <span>Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                            <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="togglePriceEdit({{ $p->id }})" title="Edit Harga">
                            </button>
                        </div>
                        <div id="price-edit-{{ $p->id }}" class="d-none">
                            <form action="{{ route('products.update-price', $p) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                @method('PUT')
                                <input type="number" name="harga" value="{{ $p->harga }}" class="form-control form-control-sm" style="width: 100px;" min="0" required>
                                <button type="submit" class="btn btn-sm btn-success">✓</button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="togglePriceEdit({{ $p->id }})">✕</button>
                            </form>
                        </div>
                    </td>
                    <td>{{ $p->satuan == 'Rp' ? 'Rp '.number_format($p->stok, 0, ',', '.') : $p->stok }}</td>
                    <td>
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(this.form)">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Data produk belum ada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function togglePriceEdit(id) {
        const displayDiv = document.getElementById(`price-display-${id}`);
        const editDiv = document.getElementById(`price-edit-${id}`);
        
        if (displayDiv.classList.contains('d-none')) {
            displayDiv.classList.remove('d-none');
            editDiv.classList.add('d-none');
        } else {
            displayDiv.classList.add('d-none');
            editDiv.classList.remove('d-none');
        }
    }
</script>
@endpush
