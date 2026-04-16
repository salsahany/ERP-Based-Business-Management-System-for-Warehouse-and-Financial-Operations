<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * List POs: Admin sees own, Finance/Owner sees all.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $purchaseOrders = PurchaseOrder::where('admin_id', $user->id)
                ->with('admin')
                ->latest()
                ->get();
        } else {
            $purchaseOrders = PurchaseOrder::with('admin')
                ->latest()
                ->get();
        }

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show create form (Admin only).
     */
    public function create()
    {
        return view('purchase-orders.create');
    }

    /**
     * Store new PO (Admin only). Status → pending_finance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe_po' => 'required|in:barang,saldo',
            'catatan_admin' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.qty_pengajuan' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'no_po' => PurchaseOrder::generateNoPo(),
                'admin_id' => auth()->id(),
                'wilayah_id' => session('active_wilayah_id'),
                'tipe_po' => $request->tipe_po,
                'status' => 'pending_finance',
                'catatan_admin' => $request->catatan_admin,
                'total_pengajuan' => 0,
            ]);

            foreach ($request->items as $itemData) {
                $harga = (int) str_replace('.', '', $itemData['harga_satuan']);
                $qty = (int) $itemData['qty_pengajuan'];
                $subtotal = $qty * $harga;

                PoItem::create([
                    'purchase_order_id' => $po->id,
                    'nama_barang' => $itemData['nama_barang'],
                    'qty_pengajuan' => $qty,
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotal,
                ]);
            }

            $po->load('items');
            $po->recalculateTotals();
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dibuat dan dikirim ke Finance.');
    }

    /**
     * Show PO detail with action buttons per role & status.
     */
    public function show($id)
    {
        $po = PurchaseOrder::with(['items', 'admin'])->findOrFail($id);

        // Admin can only see own PO
        $user = auth()->user();
        if ($user->role === 'admin' && $po->admin_id !== $user->id) {
            abort(403);
        }

        return view('purchase-orders.show', compact('po'));
    }

    /**
     * Finance validates and forwards to Owner.
     * Status: pending_finance → pending_owner
     */
    public function approveByFinance(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'pending_finance') {
            return redirect()->back()->with('error', 'PO ini tidak dalam status menunggu validasi Finance.');
        }

        $po->update([
            'status' => 'pending_owner',
            'catatan_finance' => $request->input('catatan_finance'),
            'approved_by_finance_at' => now(),
        ]);

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'PO berhasil diteruskan ke Owner untuk persetujuan.');
    }

    /**
     * Owner approves or rejects.
     * Status: pending_owner → waiting_final_adjustment OR rejected
     */
    public function approveByOwner(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'pending_owner') {
            return redirect()->back()->with('error', 'PO ini tidak dalam status menunggu persetujuan Owner.');
        }

        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'reject') {
            $po->update([
                'status' => 'rejected',
                'catatan_owner' => $request->input('catatan_owner'),
                'approved_by_owner_at' => now(),
            ]);

            return redirect()->route('purchase-orders.show', $po->id)
                ->with('info', 'PO telah ditolak oleh Owner.');
        }

        $po->update([
            'status' => 'waiting_final_adjustment',
            'catatan_owner' => $request->input('catatan_owner'),
            'approved_by_owner_at' => now(),
        ]);

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'PO disetujui Owner. Menunggu finalisasi oleh Finance.');
    }

    /**
     * Finance fills in qty_disetujui and finalizes.
     * Status: waiting_final_adjustment → approved
     */
    public function finalizeByFinance(Request $request, $id)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);

        if ($po->status !== 'waiting_final_adjustment') {
            return redirect()->back()->with('error', 'PO ini tidak dalam status finalisasi.');
        }

        $request->validate([
            'qty_disetujui' => 'required|array',
            'qty_disetujui.*' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($po, $request) {
            foreach ($po->items as $item) {
                $qtyDisetujui = $request->input('qty_disetujui.' . $item->id, 0);
                $item->update([
                    'qty_disetujui' => $qtyDisetujui,
                    'subtotal' => $qtyDisetujui * $item->harga_satuan,
                ]);
            }

            $po->load('items');
            $po->recalculateTotals();

            $po->update([
                'status' => 'approved',
                'finalized_at' => now(),
            ]);
        });

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'PO telah difinalisasi dan disetujui. Total disetujui: Rp ' . number_format($po->total_disetujui, 0, ',', '.'));
    }
}
