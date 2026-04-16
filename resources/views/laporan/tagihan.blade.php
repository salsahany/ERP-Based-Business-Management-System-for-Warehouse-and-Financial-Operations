@extends('layouts.dashboard')

@section('title', 'Laporan Tagihan Sales')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Laporan Tagihan Sales</h4>
</div>

{{-- Date Filter --}}
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('laporan.tagihan') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-0">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" 
                       value="{{ $startDate ?? now()->startOfWeek()->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" 
                       value="{{ $endDate ?? now()->endOfWeek()->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
            <div class="col-auto">
                <a href="{{ route('laporan.tagihan') }}" class="btn btn-outline-secondary btn-sm">
                    Reset
                </a>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('laporan.tagihan.pdf', ['start_date' => $startDate ?? now()->startOfWeek()->format('Y-m-d'), 'end_date' => $endDate ?? now()->endOfWeek()->format('Y-m-d')]) }}" 
                   target="_blank" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-pdf"></i> Cetak PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" style="min-width: 100px;">Tanggal</th>
                        <th rowspan="2" style="min-width: 150px;">Nama Sales</th>
                        @foreach($products as $product)
                            <th colspan="2" class="text-uppercase small">{{ $product->nama_produk }}</th>
                        @endforeach
                        <th rowspan="2" class="text-center align-middle">Bukti</th>
                        <th rowspan="2" style="min-width: 120px;">Sisa (Rp)</th>
                    </tr>
                    <tr>
                        @foreach($products as $product)
                            <th title="Tagihan" class="text-center py-1 bg-light border-end" style="font-size: 0.75rem;">T</th>
                            <th title="Pembayaran" class="text-center py-1 bg-light" style="font-size: 0.75rem;">P</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $date => $salesGroup)
                        @php
                            $dailyTotalBalance = 0;
                            // Matrix daily totals
                            $dailyProductTotals = []; 
                            foreach($products as $p) {
                                $dailyProductTotals[$p->id] = ['tagihan' => 0, 'dibayar' => 0];
                            }
                        @endphp
                        
                        @foreach($salesGroup as $salesName => $events)
                            @php
                                $totalTagihanAllProducts = 0;
                                $totalPaidAllProducts = 0;
                                
                                // Process Billings
                                $billingsByProduct = collect($events['billings'] ?? [])->groupBy('product_id');
                                // Process Payments
                                $paymentsByProduct = collect($events['payments'] ?? [])->groupBy(function($p) {
                                    return $p->barangKeluar->product_id;
                                });

                                // Extract Unique Proofs from all payments in this row (Direct Link)
                                $directProofs = collect($events['payments'] ?? [])
                                    ->map(fn($p) => $p->companyBalance)
                                    ->filter(fn($cb) => !is_null($cb) && !is_null($cb->proof));
                                
                                // Fallback: Unique Proofs by date and sales name
                                $fallbackProofs = collect($proofs[$date][$salesName] ?? []);
                                
                                // Combine and unique by ID
                                $rowProofs = $directProofs->concat($fallbackProofs)->unique('id');
                            @endphp

                            <tr>
                                {{-- Display Date only for the first sales row of the date group --}}
                                @if($loop->first)
                                    <td rowspan="{{ count($salesGroup) + 1 }}" class="align-top fw-bold text-center border-end">{{ $date }}</td>
                                @endif

                                <td class="fw-bold border-end">
                                    {{ $salesName }}
                                    @php
                                        $rowTipes = collect($events['billings'] ?? [])->pluck('tipe')->unique();
                                    @endphp
                                    @foreach($rowTipes as $tipe)
                                        <span class="badge {{ $tipe == 'logbook' ? 'bg-warning text-dark' : 'bg-primary' }}" style="font-size: 0.6rem;">
                                            {{ ucfirst($tipe ?? 'project') }}
                                        </span>
                                    @endforeach
                                </td>
                                
                                {{-- Iterate through all products to populate the matrix cells --}}
                                @foreach($products as $product)
                                    @php
                                        $productBillings = $billingsByProduct->get($product->id);
                                        $productPayments = $paymentsByProduct->get($product->id);
                                        
                                        $productTagihan = 0;
                                        $productPaid = 0;
                                        
                                        if ($productBillings) {
                                            foreach($productBillings as $item) {
                                                $lockedPrice = $item->harga_jual ?? $item->product->harga;
                                                $price = ($item->product->satuan == 'Rp') ? 1 : $lockedPrice;
                                                $productTagihan += $item->jumlah * $price;
                                            }
                                        }
                                        
                                        if ($productPayments) {
                                            foreach($productPayments as $payment) {
                                                $productPaid += $payment->amount;
                                            }
                                        }
                                        
                                        $totalTagihanAllProducts += $productTagihan;
                                        $totalPaidAllProducts += $productPaid;
                                        
                                        $dailyProductTotals[$product->id]['tagihan'] += $productTagihan;
                                        $dailyProductTotals[$product->id]['dibayar'] += $productPaid;
                                    @endphp
                                    <td class="text-end border-end" style="font-size: 0.85rem;">
                                        {!! $productTagihan > 0 ? '<span class="text-dark">' . number_format($productTagihan, 0, ',', '.') . '</span>' : '-' !!}
                                    </td>
                                    <td class="text-end border-end" style="font-size: 0.85rem;">
                                        {!! $productPaid > 0 ? '<span class="text-success">' . number_format($productPaid, 0, ',', '.') . '</span>' : '-' !!}
                                    </td>
                                @endforeach

                                <td class="text-center align-middle border-end">
                                    @if($rowProofs->isNotEmpty())
                                        @foreach($rowProofs as $proof)
                                            <a href="{{ asset($proof->proof) }}" target="_blank" class="badge bg-info text-decoration-none">
                                                Bukti
                                            </a>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>

                                @php
                                    $salesPersonDailyBalance = $totalTagihanAllProducts - $totalPaidAllProducts;
                                    $dailyTotalBalance += $salesPersonDailyBalance;
                                    
                                    // Use pre-computed cumulative balance (running total per salesperson)
                                    $cumulativeSisa = $cumulativeBalances[$date][$salesName] ?? $salesPersonDailyBalance;
                                @endphp
                                <td class="text-end fw-bold">
                                    <span class="{{ $cumulativeSisa > 0 ? 'text-danger' : ($cumulativeSisa < 0 ? 'text-success' : '') }}">
                                        {{ $cumulativeSisa != 0 ? 'Rp ' . number_format($cumulativeSisa, 0, ',', '.') : '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        {{-- Daily Total Row --}}
                        <tr class="table-secondary">
                            <td class="text-end fw-bold">TOTAL {{ $date }}</td>
                            @foreach($products as $product)
                                <td class="text-end fw-bold border-end" style="font-size: 0.85rem;">
                                    {{ $dailyProductTotals[$product->id]['tagihan'] > 0 ? number_format($dailyProductTotals[$product->id]['tagihan'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end fw-bold border-end text-success" style="font-size: 0.85rem;">
                                    {{ $dailyProductTotals[$product->id]['dibayar'] > 0 ? number_format($dailyProductTotals[$product->id]['dibayar'], 0, ',', '.') : '-' }}
                                </td>
                            @endforeach
                            <td class="border-end"></td>
                            <td class="text-end fw-bold {{ $dailyTotalBalance > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $dailyTotalBalance != 0 ? 'Rp ' . number_format($dailyTotalBalance, 0, ',', '.') : '-' }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="{{ 4 + ($products->count() * 2) }}" class="text-center text-muted p-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Belum ada data tagihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
