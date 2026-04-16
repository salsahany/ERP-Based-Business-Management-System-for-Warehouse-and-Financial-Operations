<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe_po', ['barang', 'saldo'])->default('barang');
            $table->enum('status', [
                'pending_finance',
                'pending_owner',
                'waiting_final_adjustment',
                'approved',
                'rejected',
            ])->default('pending_finance');
            $table->text('catatan_admin')->nullable();
            $table->text('catatan_finance')->nullable();
            $table->text('catatan_owner')->nullable();
            $table->bigInteger('total_pengajuan')->default(0);
            $table->bigInteger('total_disetujui')->nullable()->default(0);
            $table->timestamp('approved_by_finance_at')->nullable();
            $table->timestamp('approved_by_owner_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
