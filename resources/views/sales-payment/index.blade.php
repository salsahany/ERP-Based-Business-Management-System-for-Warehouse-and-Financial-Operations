@extends('layouts.dashboard')

@section('title', 'Tagihan Sales')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Tagihan Sales (Pembayaran)</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @forelse($salesData as $namaSales => $items)
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $namaSales }}</h5>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('sales-payment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="nama_peminta" value="{{ $namaSales }}">
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Barang / Saldo</th>
                                        <th>Total Tagihan</th>
                                        <th>Sudah Bayar</th>
                                        <th>Sisa Tagihan</th>
                                        <th>Tanggal Bayar</th>
                                        <th width="200">Bayar (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        @php
                                            $lockedPrice = $item->harga_jual ?? $item->product->harga;
                                            $price = ($item->product->satuan == 'Rp') ? 1 : $lockedPrice;
                                            $subtotal = $item->jumlah * $price;
                                            $remaining = $subtotal - $item->amount_paid;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->tanggal_keluar }}</td>
                                            <td>
                                                {{ $item->product->nama_produk }}
                                                <span class="badge {{ $item->tipe == 'logbook' ? 'bg-warning text-dark' : 'bg-primary' }}" style="font-size: 0.6rem;">
                                                    {{ ucfirst($item->tipe ?? 'project') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $item->product->satuan == 'Rp' ? 'Rp '.number_format($item->jumlah,0,',','.') : $item->jumlah.' '.$item->product->satuan . ' @ '.number_format($price,0,',','.') }}
                                                    @if($item->harga_jual && $item->product->satuan != 'Rp')
                                                        <span class="badge bg-info text-dark" style="font-size: 0.6rem;">Locked</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                            <td class="text-success">Rp {{ number_format($item->amount_paid, 0, ',', '.') }}</td>
                                            <td class="text-danger fw-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
                                            <td>
                                                <input type="date" 
                                                       name="payment_dates[{{ $item->id }}]" 
                                                       class="form-control" 
                                                       value="{{ date('Y-m-d') }}">
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="payments[{{ $item->id }}]" 
                                                       class="form-control payment-input" 
                                                       placeholder="0">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="p-3">
                                            <div class="d-flex justify-content-end align-items-center gap-3">
                                                <div>
                                                    <label for="proof_{{ $loop->index }}" class="form-label mb-1 small text-muted">Upload Bukti Pembayaran (JPG/PDF)</label>
                                                    <input type="file" 
                                                           name="proof" 
                                                           id="proof_{{ $loop->index }}" 
                                                           class="form-control form-control-sm" 
                                                           accept=".jpg,.jpeg,.png,.pdf">
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Proses pembayaran untuk {{ $namaSales }}?')">
                                                        Simpan Pembayaran {{ $namaSales }}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-muted p-5">
                <h5>Tidak ada tagihan yang belum dibayar.</h5>
            </div>
        @endforelse

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delegate event for performance or just loop all
        const inputs = document.querySelectorAll('.payment-input');
        
        inputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9]/g, '');
                if (value) {
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                } else {
                    this.value = '';
                }
            });
        });
    });
</script>
@endpush
