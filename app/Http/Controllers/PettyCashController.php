<?php

namespace App\Http\Controllers;

use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    /**
     * List petty cash entries (filtered by WilayahScope automatically).
     */
    public function index(Request $request)
    {
        $query = PettyCash::with('admin')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pettyCash = $query->get();

        return view('petty-cash.index', compact('pettyCash'));
    }

    /**
     * Show create form (Admin only).
     */
    public function create()
    {
        $kategoriList = PettyCash::KATEGORI_LIST;
        return view('petty-cash.create', compact('kategoriList'));
    }

    /**
     * Store new petty cash entry (Admin only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:255',
            'nominal' => 'required|string',
            'keterangan' => 'nullable|string',
            'bukti_nota' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $nominal = (int) str_replace('.', '', $request->nominal);

        $buktiPath = null;
        if ($request->hasFile('bukti_nota')) {
            $file = $request->file('bukti_nota');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/petty-cash', $filename, 'public');
            $buktiPath = 'storage/' . $path;
        }

        PettyCash::create([
            'wilayah_id' => session('active_wilayah_id'),
            'admin_id' => auth()->id(),
            'kategori' => $request->kategori,
            'nominal' => $nominal,
            'keterangan' => $request->keterangan,
            'bukti_nota' => $buktiPath,
            'status' => 'pending',
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('petty-cash.index')
            ->with('success', 'Pengeluaran kas kecil berhasil ditambahkan.');
    }

    /**
     * Show detail + approval panel for Finance.
     */
    public function show($id)
    {
        $pettyCash = PettyCash::with(['admin', 'approver', 'wilayah'])->findOrFail($id);
        return view('petty-cash.show', compact('pettyCash'));
    }

    /**
     * Approve or reject (Finance only).
     */
    public function approve(Request $request, $id)
    {
        $pettyCash = PettyCash::findOrFail($id);

        if ($pettyCash->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya entry dengan status pending yang bisa di-approve/reject.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_finance' => 'nullable|string',
        ]);

        $pettyCash->update([
            'status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'catatan_finance' => $request->catatan_finance,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $statusLabel = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return redirect()->route('petty-cash.show', $id)
            ->with('success', "Petty cash berhasil {$statusLabel}.");
    }

    /**
     * Summary report grouped by kategori (Finance/Owner).
     */
    public function summary(Request $request)
    {
        $query = PettyCash::query()->where('status', 'approved');

        // Date filter
        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        $summary = $query->select('kategori', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(nominal) as total'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $summary->sum('total');

        return view('petty-cash.summary', compact('summary', 'grandTotal'));
    }
}
