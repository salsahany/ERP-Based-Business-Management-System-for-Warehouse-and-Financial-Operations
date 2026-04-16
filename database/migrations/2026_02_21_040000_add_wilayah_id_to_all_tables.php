<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add wilayah_id to tables that don't have it yet
        if (!Schema::hasColumn('company_balances', 'wilayah_id')) {
            Schema::table('company_balances', function (Blueprint $table) {
                $table->foreignId('wilayah_id')->nullable()->after('id')->constrained('wilayahs')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('purchase_orders', 'wilayah_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->foreignId('wilayah_id')->nullable()->after('id')->constrained('wilayahs')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        foreach (['company_balances', 'purchase_orders'] as $table) {
            if (Schema::hasColumn($table, 'wilayah_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['wilayah_id']);
                    $t->dropColumn('wilayah_id');
                });
            }
        }
    }
};
