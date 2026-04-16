<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalProduk'  => Product::count(),
            'totalMasuk'   => BarangMasuk::sum('jumlah'),
            'totalKeluar'  => BarangKeluar::sum('jumlah'),
            'totalBalance' => \App\Models\CompanyBalance::sum('amount'), // Total Saldo Perusahaan
        ]);
    }
}
