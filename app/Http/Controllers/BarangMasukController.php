<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuk = BarangMasuk::with('product')->latest()->get();
        return view('barang-masuk.index', compact('barangMasuk'));
    }

    public function create()
    {
        $products = Product::all();
        return view('barang-masuk.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Hapus separator titik jika ada (format rupiah)
        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace('.', '', $request->jumlah)
            ]);
        }
        if ($request->has('harga')) {
            $request->merge([
                'harga' => str_replace('.', '', $request->harga)
            ]);
        }

        $request->validate([
            'nama_produk' => 'required',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'harga' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Cari produk berdasarkan nama
            $product = Product::where('nama_produk', $request->nama_produk)->first();

            // Jika produk belum ada, buat baru
            if (!$product) {
                $request->validate([
                    'kode_produk' => 'required|unique:products,kode_produk',
                    'satuan' => 'required',
                ]);

                $product = Product::create([
                    'nama_produk' => $request->nama_produk,
                    'kode_produk' => $request->kode_produk,
                    'satuan' => $request->satuan,
                    'stok' => 0,
                    'harga' => $request->harga ?? 0,
                    'user_id' => auth()->id(),
                    'wilayah_id' => session('active_wilayah_id'),
                ]);
            } else {
                // Jika produk sudah ada, update harga jika diinput
                if ($request->filled('harga')) {
                    $product->update(['harga' => $request->harga]);
                }
            }

            // Simpan barang masuk
            BarangMasuk::create([
                'user_id' => auth()->id(),
                'wilayah_id' => session('active_wilayah_id'),
                'product_id' => $product->id,
                'jumlah' => $request->jumlah,
                'tanggal_masuk' => $request->tanggal_masuk,
                'keterangan' => $request->keterangan,
            ]);

            // Tambah stok produk
            $product->increment('stok', $request->jumlah);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil disimpan');
    }
}

