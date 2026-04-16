@extends('layouts.dashboard')

@section('title', 'Tambah Barang Masuk')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Tambah Barang Masuk</h4>
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

        <form method="POST" action="{{ route('barang-masuk.store') }}">
            @csrf

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
                <label class="form-label">Nama Produk</label>
                <input type="text" 
                       name="nama_produk" 
                       class="form-control" 
                       list="product_list" 
                       value="{{ old('nama_produk') }}" 
                       required 
                       autocomplete="off">
                <datalist id="product_list">
                    @foreach($products as $product)
                        <option value="{{ $product->nama_produk }}">
                    @endforeach
                </datalist>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Produk (Untuk Produk Baru)</label>
                    <input type="text" 
                           name="kode_produk" 
                           class="form-control" 
                           value="{{ old('kode_produk') }}">
                    <small class="text-muted">Kosongkan jika produk sudah ada</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Satuan (Untuk Produk Baru)</label>
                    <input type="text" 
                           name="satuan" 
                           id="satuan"
                           class="form-control" 
                           value="{{ old('satuan', 'pcs') }}">
                     <small class="text-muted">Kosongkan jika produk sudah ada</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Masuk</label>
                <input type="number"
                       name="jumlah"
                       id="jumlah"
                       class="form-control"
                       min="1"
                       value="{{ old('jumlah') }}"
                       required>
            </div>

            <div class="mb-3" id="harga_container">
                <label class="form-label">Harga Jual (per pcs/satuan)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" 
                           id="harga" 
                           name="harga" 
                           class="form-control" 
                           placeholder="Contoh: 5.000"
                           value="{{ old('harga') }}">
                </div>
                <small class="text-muted">Harga jual ini akan digunakan untuk hitungan tagihan sales.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Masuk</label>
                <input type="date"
                       name="tanggal_masuk"
                       class="form-control"
                       value="{{ old('tanggal_masuk', date('Y-m-d')) }}"
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

                <a href="{{ route('barang-masuk.index') }}"
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
        const satuanInput = document.getElementById('satuan');
        const jumlahInput = document.getElementById('jumlah');
        const hargaContainer = document.getElementById('harga_container');
        const hargaInput = document.getElementById('harga');

        function updateForm() {
            if (jenisSaldo.checked) {
                // Mode Saldo
                satuanInput.value = 'Rp';
                satuanInput.readOnly = true;
                
                jumlahInput.type = 'text'; // Allow formatting
                jumlahInput.placeholder = 'Contoh: 2.000.000';
                
                // Hide price input for Saldo (implicitly 1:1)
                hargaContainer.style.display = 'none';
                hargaInput.value = '';
            } else {
                // Mode Barang
                satuanInput.value = 'pcs';
                satuanInput.readOnly = false;
                
                jumlahInput.type = 'number';
                jumlahInput.placeholder = '';
                
                // Show price input for Barang
                hargaContainer.style.display = 'block';
            }
        }

        // Event Listeners
        jenisBarang.addEventListener('change', updateForm);
        jenisSaldo.addEventListener('change', updateForm);

        // Format Currency on Input (Only if Saldo for Jumlah, or always for Harga)
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

        hargaInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                this.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                this.value = '';
            }
        });

        // Initialize
        updateForm();
    });
</script>

    </div>
</div>

@endsection
