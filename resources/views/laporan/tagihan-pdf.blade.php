<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tagihan Sales</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #333; }
        .header h1 { font-size: 16px; margin-bottom: 3px; }
        .header p { font-size: 10px; color: #666; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #aaa; padding: 4px 5px; }
        th { background-color: #e9ecef; font-weight: bold; text-align: center; font-size: 8px; }
        td { font-size: 8px; }

        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; }
        .badge-primary { background-color: #0d6efd; color: #fff; }
        .badge-warning { background-color: #ffc107; color: #333; }

        .total-row { background-color: #e9ecef; font-weight: bold; }
        .date-cell { font-weight: bold; text-align: center; vertical-align: top; }

        .footer { margin-top: 10px; text-align: right; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Tagihan Sales</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="min-width: 60px;">Tanggal</th>
                <th rowspan="2" style="min-width: 80px;">Nama Sales</th>
                @foreach($products as $product)
                    <th colspan="2">{{ $product->nama_produk }}</th>
                @endforeach
                <th rowspan="2">Sisa (Rp)</th>
            </tr>
            <tr>
                @foreach($products as $product)
                    <th>T</th>
                    <th>P</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $date => $salesGroup)
                @php
                    $dailyTotalBalance = 0;
                    $dailyProductTotals = []; 
                    foreach($products as $p) {
                        $dailyProductTotals[$p->id] = ['tagihan' => 0, 'dibayar' => 0];
                    }
                @endphp

                @foreach($salesGroup as $salesName => $events)
                    @php
                        $totalTagihanAllProducts = 0;
                        $totalPaidAllProducts = 0;
                        
                        $billingsByProduct = collect($events['billings'] ?? [])->groupBy('product_id');
                        $paymentsByProduct = collect($events['payments'] ?? [])->groupBy(function($p) {
                            return $p->barangKeluar->product_id;
                        });
                    @endphp

                    <tr>
                        @if($loop->first)
                            <td rowspan="{{ count($salesGroup) + 1 }}" class="date-cell">{{ $date }}</td>
                        @endif

                        <td class="fw-bold">
                            {{ $salesName }}
                            @php $rowTipes = collect($events['billings'] ?? [])->pluck('tipe')->unique(); @endphp
                            @foreach($rowTipes as $tipe)
                                <span class="badge {{ $tipe == 'logbook' ? 'badge-warning' : 'badge-primary' }}">
                                    {{ ucfirst($tipe ?? 'project') }}
                                </span>
                            @endforeach
                        </td>

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
                            <td class="text-end">
                                {!! $productTagihan > 0 ? number_format($productTagihan, 0, ',', '.') : '-' !!}
                            </td>
                            <td class="text-end">
                                {!! $productPaid > 0 ? '<span class="text-success">' . number_format($productPaid, 0, ',', '.') . '</span>' : '-' !!}
                            </td>
                        @endforeach

                        @php
                            $salesPersonDailyBalance = $totalTagihanAllProducts - $totalPaidAllProducts;
                            $dailyTotalBalance += $salesPersonDailyBalance;
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
                <tr class="total-row">
                    <td class="text-end fw-bold">TOTAL {{ $date }}</td>
                    @foreach($products as $product)
                        <td class="text-end">
                            {{ $dailyProductTotals[$product->id]['tagihan'] > 0 ? number_format($dailyProductTotals[$product->id]['tagihan'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end text-success">
                            {{ $dailyProductTotals[$product->id]['dibayar'] > 0 ? number_format($dailyProductTotals[$product->id]['dibayar'], 0, ',', '.') : '-' }}
                        </td>
                    @endforeach
                    <td class="text-end fw-bold {{ $dailyTotalBalance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $dailyTotalBalance != 0 ? 'Rp ' . number_format($dailyTotalBalance, 0, ',', '.') : '-' }}
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="{{ 3 + ($products->count() * 2) }}" class="text-center" style="padding: 20px;">
                        Belum ada data tagihan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
