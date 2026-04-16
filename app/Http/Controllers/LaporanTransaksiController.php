<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class LaporanTransaksiController extends Controller
{
    public function barangMasuk(Request $request)
    {
        $data = BarangMasuk::with('product')
            ->when($request->start_date, function ($query) use ($request) {
                $query->whereDate('tanggal_masuk', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->whereDate('tanggal_masuk', '<=', $request->end_date);
            })
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        return view('laporan.barang-masuk', compact('data'));
    }

    public function barangKeluar(Request $request)
    {
        $data = BarangKeluar::with('product')
            ->when($request->start_date, function ($query) use ($request) {
                $query->whereDate('tanggal_keluar', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->whereDate('tanggal_keluar', '<=', $request->end_date);
            })
            ->orderBy('tanggal_keluar', 'desc')
            ->get();

        return view('laporan.barang-keluar', compact('data'));
    }
}
