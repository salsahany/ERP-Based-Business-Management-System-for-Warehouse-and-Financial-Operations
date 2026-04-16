<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\CompanyBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesPaymentController extends Controller
{
    public function index()
    {
        // Get all unpaid items grouped by nama_peminta
        $salesData = BarangKeluar::with('product')
            ->where('status', 'unpaid')
            ->get()
            ->groupBy('nama_peminta');

        return view('sales-payment.index', compact('salesData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_peminta' => 'required|string',
            'payments' => 'required|array',
            'payments.*' => 'nullable|string',
            'payment_dates' => 'nullable|array',
            'payment_dates.*' => 'nullable|date',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        $totalPaidGlobal = 0;
        $countItemsPaid = 0;
        $proofPath = null;

        // Handle File Upload
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/payments', $filename, 'public');
            $proofPath = 'storage/' . $path;
        }

        DB::transaction(function () use ($request, &$totalPaidGlobal, &$countItemsPaid, $proofPath) {
            $paymentRecords = [];
            
            foreach ($request->payments as $itemId => $amountRaw) {
                // Sanitize input (remove dots)
                $amount = (int) str_replace('.', '', $amountRaw);

                if ($amount > 0) {
                    $item = BarangKeluar::with('product')->find($itemId);

                    if ($item && $item->nama_peminta == $request->nama_peminta && $item->status == 'unpaid') {
                        // Use locked price if available, otherwise fallback to current product price
                        $lockedPrice = $item->harga_jual ?? $item->product->harga;
                        $price = ($item->product->satuan == 'Rp') ? 1 : $lockedPrice;
                        
                        $itemTotalCost = $item->jumlah * $price;
                        $itemRemaining = $itemTotalCost - $item->amount_paid;
                        $payAmount = min($amount, $itemRemaining);

                        if ($payAmount > 0) {
                            $item->amount_paid += $payAmount;
                            if ($item->amount_paid >= $itemTotalCost) {
                                $item->status = 'paid';
                            }
                            $item->save();
                            
                            $paymentDate = $request->payment_dates[$itemId] ?? now()->format('Y-m-d');
                            
                            $paymentRecords[] = [
                                'barang_keluar_id' => $item->id,
                                'amount' => $payAmount,
                                'payment_date' => $paymentDate,
                                'user_id' => $item->user_id, // Link to the owner of the item
                            ];
                            
                            $totalPaidGlobal += $payAmount;
                            $countItemsPaid++;
                        }
                    }
                }
            }

            if ($totalPaidGlobal > 0) {
                // Determine the user_id for this batch of payments (we assume they all belong to the same salesperson/owner)
                $ownerUserId = (!empty($paymentRecords)) ? $paymentRecords[0]['user_id'] : auth()->id();

                // Create Company Balance record
                $balance = \App\Models\CompanyBalance::create([
                    'user_id' => $ownerUserId,
                    'wilayah_id' => session('active_wilayah_id'),
                    'amount' => $totalPaidGlobal,
                    'sales_name' => $request->nama_peminta,
                    'description' => 'Pembayaran Sales: ' . $request->nama_peminta . ' (' . $countItemsPaid . ' items)',
                    'proof' => $proofPath,
                ]);

                // Record historical payments linked to this balance
                foreach ($paymentRecords as $record) {
                    $record['company_balance_id'] = $balance->id;
                    \App\Models\SalesPayment::create($record);
                }
            }
        });

        if ($totalPaidGlobal > 0) {
            return redirect()->route('sales-payment.index')
                ->with('success', 'Pembayaran berhasil diproses. Total: Rp ' . number_format($totalPaidGlobal, 0, ',', '.'));
        } else {
            return redirect()->route('sales-payment.index')
                ->with('info', 'Tidak ada pembayaran yang diproses.');
        }
    }
}
