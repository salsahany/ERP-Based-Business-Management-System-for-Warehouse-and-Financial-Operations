<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBalance extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\WilayahScope);
    }

    protected $fillable = [
        'user_id',
        'wilayah_id',
        'amount',
        'sales_name',
        'description',
        'proof',
    ];
}
