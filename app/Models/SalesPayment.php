<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\UserScope);
    }

    protected $fillable = [
        'barang_keluar_id',
        'amount',
        'payment_date',
        'company_balance_id',
        'user_id',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class);
    }

    public function companyBalance()
    {
        return $this->belongsTo(CompanyBalance::class);
    }
}
