@extends('layouts.dashboard')

@section('title', 'Buat Purchase Order')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Buat Purchase Order Baru</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('purchase-orders.store') }}" id="poForm">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipe PO</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_po" id="tipe_barang" value="barang" {{ old('tipe_po', 'barang') == 'barang' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_barang">Barang</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_po" id="tipe_saldo" value="saldo" {{ old('tipe_po') == 'saldo' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_saldo">Saldo</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)">{{ old('catatan_admin') }}</textarea>
            </div>

            <hr>
            <h5 class="fw-bold mb-3">Item PO</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang / Keterangan</th>
                            <th style="width: 120px;">Qty</th>
                            <th style="width: 180px;">Harga Satuan (Rp)</th>
                            <th style="width: 160px;">Subtotal</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[0][nama_barang]" class="form-control form-control-sm" required placeholder="Nama barang">
                            </td>
                            <td>
                                <input type="number" name="items[0][qty_pengajuan]" class="form-control form-control-sm qty-input" min="1" value="1" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][harga_satuan]" class="form-control form-control-sm harga-input" required placeholder="0">
                            </td>
                            <td>
                                <span class="subtotal-display fw-bold">Rp 0</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled>×</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total Pengajuan:</td>
                            <td colspan="2"><span id="totalPengajuan" class="fw-bold text-primary">Rp 0</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="addRow">
                + Tambah Item
            </button>

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Kirim PO ke Finance
                </button>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;

    // Add row
    document.getElementById('addRow').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const row = document.createElement('tr');
        row.classList.add('item-row');
        row.innerHTML = `
            <td><input type="text" name="items[${rowIndex}][nama_barang]" class="form-control form-control-sm" required placeholder="Nama barang"></td>
            <td><input type="number" name="items[${rowIndex}][qty_pengajuan]" class="form-control form-control-sm qty-input" min="1" value="1" required></td>
            <td><input type="text" name="items[${rowIndex}][harga_satuan]" class="form-control form-control-sm harga-input" required placeholder="0"></td>
            <td><span class="subtotal-display fw-bold">Rp 0</span></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>
        `;
        tbody.appendChild(row);
        rowIndex++;
        updateRemoveButtons();
    });

    // Remove row (delegated)
    document.getElementById('itemsBody').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateRemoveButtons();
            calculateTotal();
        }
    });

    // Format currency + calculate (delegated)
    document.getElementById('itemsBody').addEventListener('input', function(e) {
        if (e.target.classList.contains('harga-input')) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                e.target.value = new Intl.NumberFormat('id-ID').format(value);
            }
        }
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseInt(row.querySelector('.qty-input')?.value) || 0;
            const hargaRaw = row.querySelector('.harga-input')?.value.replace(/[^0-9]/g, '') || '0';
            const harga = parseInt(hargaRaw);
            const subtotal = qty * harga;
            total += subtotal;
            const display = row.querySelector('.subtotal-display');
            if (display) display.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        });
        document.getElementById('totalPengajuan').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            row.querySelector('.remove-row').disabled = rows.length <= 1;
        });
    }
});
</script>

@endsection
