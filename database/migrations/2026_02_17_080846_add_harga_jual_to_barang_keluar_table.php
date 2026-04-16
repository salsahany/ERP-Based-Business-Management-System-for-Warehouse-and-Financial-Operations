<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->integer('harga_jual')->nullable()->after('jumlah');
        });

        // Backfill existing data with current product price
        DB::statement("UPDATE barang_keluar b JOIN products p ON b.product_id = p.id SET b.harga_jual = p.harga WHERE b.harga_jual IS NULL");
        
        // For 'Saldo' items (where product name might imply money or unit is Rp), we should ideally ensure price is 1.
        // But the previous logic used product price. If product is 'Saldo', price should be 1. 
        // Let's assume the join above covered it if the Product 'Saldo' has price 1.
        // If 'Saldo' product has price 0 or null, we might need to fix it.
        // However, based on previous steps, 'Saldo' product likely has satuna='Rp'.
        DB::statement("UPDATE barang_keluar b JOIN products p ON b.product_id = p.id SET b.harga_jual = 1 WHERE p.satuan = 'Rp'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }
};
