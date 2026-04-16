<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    protected $table = 'sales_persons';

    protected $fillable = [
        'user_id',
        'wilayah_id',
        'nama',
        'no_hp',
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
}
