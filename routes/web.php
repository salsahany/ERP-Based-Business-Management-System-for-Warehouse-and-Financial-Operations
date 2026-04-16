<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\LaporanTransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesPaymentController;
use App\Http\Controllers\LaporanTagihanController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PettyCashController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::put('/products/{product}/update-price', [ProductController::class, 'updatePrice'])->name('products.update-price');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('barang-masuk', BarangMasukController::class)
        ->only(['index','create','store']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('barang-keluar', BarangKeluarController::class)
        ->only(['index','create','store']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan/stok', [LaporanStokController::class, 'index'])
        ->name('laporan.stok');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan/barang-masuk', [LaporanTransaksiController::class, 'barangMasuk'])
        ->name('laporan.barang-masuk');

    Route::get('/laporan/barang-keluar', [LaporanTransaksiController::class, 'barangKeluar'])
        ->name('laporan.barang-keluar');

    Route::get('/laporan/tagihan-sales', [LaporanTagihanController::class, 'index'])
        ->name('laporan.tagihan');

    Route::get('/laporan/tagihan-sales/pdf', [LaporanTagihanController::class, 'exportPdf'])
        ->name('laporan.tagihan.pdf');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/wilayah/switch/{wilayahId}', [WilayahController::class, 'switchWilayah'])->name('wilayah.switch');
    Route::resource('sales-payment', SalesPaymentController::class)
        ->only(['index', 'store']);
    Route::resource('sales-persons', \App\Http\Controllers\SalesPersonController::class);
});

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
    Route::delete('/wilayah/{id}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');
    Route::post('/wilayah/{id}/assign-admins', [WilayahController::class, 'assignAdmins'])->name('wilayah.assign-admins');
});

Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])
    ->name('laporan.transaksi');

// Purchase Order Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('purchase-orders', PurchaseOrderController::class)
        ->only(['index', 'create', 'store', 'show']);
});

Route::middleware(['auth', 'role:finance'])->group(function () {
    Route::post('/purchase-orders/{id}/approve-finance', [PurchaseOrderController::class, 'approveByFinance'])
        ->name('purchase-orders.approve-finance');
    Route::post('/purchase-orders/{id}/finalize', [PurchaseOrderController::class, 'finalizeByFinance'])
        ->name('purchase-orders.finalize');
});

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::post('/purchase-orders/{id}/approve-owner', [PurchaseOrderController::class, 'approveByOwner'])
        ->name('purchase-orders.approve-owner');
});

// Petty Cash Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('petty-cash', PettyCashController::class)
        ->only(['index', 'create', 'store', 'show']);
});

Route::middleware(['auth', 'role:finance'])->group(function () {
    Route::post('/petty-cash/{id}/approve', [PettyCashController::class, 'approve'])
        ->name('petty-cash.approve');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/petty-cash-summary', [PettyCashController::class, 'summary'])
        ->name('petty-cash.summary');
});

require __DIR__.'/auth.php';
