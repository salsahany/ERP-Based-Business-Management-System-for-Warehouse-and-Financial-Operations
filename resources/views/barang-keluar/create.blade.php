@extends('layouts.dashboard')

@section('title', 'Tambah Barang Keluar')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Tambah Barang Keluar</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        {{-- Alert error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('barang-keluar.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Peminta (Sales)</label>
                <select name="nama_peminta" class="form-select" required>
                    <option value="">-- Pilih Sales --</option>
                    @foreach($salesPersons as $sales)
                        <option value="{{ $sales->nama }}" {{ old('nama_peminta') == $sales->nama ? 'selected' : '' }}>
                            {{ $sales->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipe</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipe" id="tipe_project" value="project" {{ old('tipe', 'project') == 'project' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tipe_project">Project</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipe" id="tipe_logbook" value="logbook" {{ old('tipe') == 'logbook' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tipe_logbook">Logbook</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis" id="jenis_barang" value="barang" checked>
                        <label class="form-check-label" for="jenis_barang">Barang</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis" id="jenis_saldo" value="saldo">
                        <label class="form-check-label" for="jenis_saldo">Saldo</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Produk</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-satuan="{{ $product->satuan }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->nama_produk }} (Stok: {{ $product->satuan == 'Rp' ? 'Rp ' . number_format($product->stok, 0, ',', '.') : $product->stok . ' ' . $product->satuan }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Keluar</label>
                <input type="number"
                       name="jumlah"
                       id="jumlah"
                       class="form-control"
                       min="1"
                       value="{{ old('jumlah') }}"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Keluar</label>
                <input type="date"
                       name="tanggal_keluar"
                       class="form-control"
                       value="{{ old('tanggal_keluar', date('Y-m-d')) }}"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan"
                          class="form-control"
                          rows="3">{{ old('keterangan') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('barang-keluar.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisBarang = document.getElementById('jenis_barang');
        const jenisSaldo = document.getElementById('jenis_saldo');
        const jumlahInput = document.getElementById('jumlah');
        const productSelect = document.getElementById('product_id');

        function updateForm() {
            if (jenisSaldo.checked) {
                // Mode Saldo
                jumlahInput.type = 'text'; // Allow formatting
                jumlahInput.placeholder = 'Contoh: 2.000.000';
            } else {
                // Mode Barang
                jumlahInput.type = 'number';
                jumlahInput.placeholder = '';
            }
        }

        // Event Listeners
        jenisBarang.addEventListener('change', updateForm);
        jenisSaldo.addEventListener('change', updateForm);

        // Auto-select based on product unit if possible
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const satuan = selectedOption.getAttribute('data-satuan');
            if (satuan === 'Rp') {
                jenisSaldo.checked = true;
                updateForm();
            } else if (satuan) {
                jenisBarang.checked = true;
                updateForm();
            }
        });

        // Format Currency on Input (Only if Saldo)
        jumlahInput.addEventListener('input', function(e) {
            if (jenisSaldo.checked) {
                let value = this.value.replace(/[^0-9]/g, '');
                if (value) {
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                } else {
                    this.value = '';
                }
            }
        });

        // Initialize
        updateForm();
    });
</script>

    </div>
</div>

@endsection
