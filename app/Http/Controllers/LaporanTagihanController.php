<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\CompanyBalance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanTagihanController extends Controller
{
    /**
     * Build report data filtered by optional date range.
     */
    private function buildReportData($startDate = null, $endDate = null)
    {
        // 1. Fetch Billing Events (Barang Keluar)
        $itemsQuery = BarangKeluar::with('product');
        if ($startDate && $endDate) {
            $itemsQuery->whereBetween('tanggal_keluar', [$startDate, $endDate]);
        }
        $items = $itemsQuery->get();
        
        // 2. Fetch Payment Events
        $paymentsQuery = \App\Models\SalesPayment::with(['barangKeluar.product', 'companyBalance' => function($query) {
            $query->withoutGlobalScopes();
        }]);
        if ($startDate && $endDate) {
            $paymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
        $payments = $paymentsQuery->get();

        // 3. Extract all unique products for columns
        $products = $items->pluck('product')->unique('id')->values();

        // 4. Combine into Event Groups
        $combinedData = [];

        foreach ($items as $item) {
            $date = $item->tanggal_keluar;
            $salesName = $item->nama_peminta;
            $combinedData[$date][$salesName]['billings'][] = $item;
        }

        foreach ($payments as $payment) {
            if (!$payment->barangKeluar) {
                continue;
            }
            $date = $payment->payment_date;
            $salesName = $payment->barangKeluar->nama_peminta;
            $combinedData[$date][$salesName]['payments'][] = $payment;
        }

        // Sort ascending to compute running balance
        ksort($combinedData);

        // 5. Compute running cumulative balance per salesperson
        $runningBySales = [];
        $cumulativeBalances = [];

        foreach ($combinedData as $date => $salesGroup) {
            foreach ($salesGroup as $salesName => $events) {
                if (!isset($runningBySales[$salesName])) {
                    $runningBySales[$salesName] = 0;
                }

                $rowTagihan = 0;
                $rowBayar = 0;

                foreach ($events['billings'] ?? [] as $item) {
                    $lockedPrice = $item->harga_jual ?? $item->product->harga;
                    $price = ($item->product->satuan == 'Rp') ? 1 : $lockedPrice;
                    $rowTagihan += $item->jumlah * $price;
                }

                foreach ($events['payments'] ?? [] as $payment) {
                    $rowBayar += $payment->amount;
                }

                $runningBySales[$salesName] += ($rowTagihan - $rowBayar);
                $cumulativeBalances[$date][$salesName] = $runningBySales[$salesName];
            }
        }

        // Sort descending for display
        krsort($combinedData);

        $reports = collect($combinedData);

        // 6. Get Payment Proofs
        $proofsRaw = CompanyBalance::withoutGlobalScopes()
            ->whereNotNull('sales_name')
            ->whereNotNull('proof')
            ->get();
            
        $proofs = $proofsRaw->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($dateGroup) {
            return $dateGroup->groupBy('sales_name');
        });

        return compact('reports', 'products', 'proofs', 'cumulativeBalances');
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = $this->buildReportData($startDate, $endDate);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        return view('laporan.tagihan', $data);
    }

    public function exportPdf(Request $request)
    {
        // Default to current week (Monday to Sunday)
        $startDate = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));

        $data = $this->buildReportData($startDate, $endDate);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        $pdf = Pdf::loadView('laporan.tagihan-pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_Tagihan_' . $startDate . '_sd_' . $endDate . '.pdf';

        return $pdf->stream($filename);
    }
}
