<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $barangKeluar = BarangKeluar::with('product')->latest()->get();
        return view('barang-keluar.index', compact('barangKeluar'));
    }

    public function create()
    {
        $products = Product::all();
        $salesPersons = \App\Models\SalesPerson::all();
        return view('barang-keluar.create', compact('products', 'salesPersons'));
    }

    public function store(Request $request)
    {
        // Hapus separator titik jika ada (format rupiah)
        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace('.', '', $request->jumlah)
            ]);
        }

        $request->validate([
            'nama_peminta' => 'required|string|max:255',
            'tipe' => 'required|in:project,logbook',
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

    $product = Product::findOrFail($request->product_id);

    // Cross-wilayah validation: product must belong to active wilayah
    $activeWilayah = session('active_wilayah_id');
    if ($activeWilayah && $product->wilayah_id != $activeWilayah) {
        return redirect()->back()->with('error', 'Produk ini bukan milik wilayah aktif Anda.');
    }

    // cek stok
    if ($product->satuan != 'Rp' && $request->jumlah > $product->stok) {
         return redirect()->back()->with('error', 'Stok tidak mencukupi!');
    } elseif ($product->satuan == 'Rp' && $request->jumlah > $product->stok) {
         return redirect()->back()->with('error', 'Saldo perusahaan tidak mencukupi!');
    }

    // kurangi stok produk
    $product->stok -= $request->jumlah;
    $product->save();

    // Tentukan harga jual saat ini
    $hargaJual = ($product->satuan == 'Rp') ? 1 : $product->harga;

    // simpan data barang keluar
    BarangKeluar::create([
        'user_id' => auth()->id(),
        'wilayah_id' => session('active_wilayah_id'),
        'nama_peminta' => $request->nama_peminta,
        'tipe' => $request->tipe,
        'product_id' => $product->id,
        'jumlah' => $request->jumlah,
        'harga_jual' => $hargaJual,
        'tanggal_keluar' => $request->tanggal_keluar,
        'keterangan' => $request->keterangan,
        'status' => 'unpaid',
        'amount_paid' => 0,
    ]);

    return redirect()->route('barang-keluar.index')
                     ->with('success', 'Barang keluar berhasil disimpan');
}
}
