<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'wilayah_id',
        'kode_produk',
        'nama_produk',
        'satuan',
        'harga',
        'stok',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\WilayahScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }
    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }
}
