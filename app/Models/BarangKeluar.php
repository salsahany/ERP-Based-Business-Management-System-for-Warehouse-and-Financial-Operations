<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'user_id',
        'wilayah_id',
        'product_id',
        'nama_peminta',
        'jumlah',
        'harga_jual',
        'status',
        'amount_paid',
        'tanggal_keluar',
        'keterangan',
        'tipe',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\WilayahScope);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesPayments()
    {
        return $this->hasMany(SalesPayment::class);
    }
}
