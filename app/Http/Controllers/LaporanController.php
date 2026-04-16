<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function transaksi(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $barangMasuk = BarangMasuk::with('product')
            ->when($start, fn($q) => $q->whereDate('tanggal_masuk', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('tanggal_masuk', '<=', $end))
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal'    => $item->tanggal_masuk,
                    'produk'     => $item->product->nama_produk,
                    'jenis'      => 'Masuk',
                    'jumlah'     => $item->jumlah,
                    'keterangan' => $item->keterangan,
                ];
            });

        $barangKeluar = BarangKeluar::with('product')
            ->when($start, fn($q) => $q->whereDate('tanggal_keluar', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('tanggal_keluar', '<=', $end))
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal'    => $item->tanggal_keluar,
                    'produk'     => $item->product->nama_produk,
                    'jenis'      => 'Keluar',
                    'jumlah'     => $item->jumlah,
                    'keterangan' => $item->keterangan,
                ];
            });

        $data = $barangMasuk
            ->merge($barangKeluar)
            ->sortBy('tanggal');

        return view('laporan.transaksi', compact('data'));
    }
}
