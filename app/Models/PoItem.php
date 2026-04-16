<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'nama_barang',
        'qty_pengajuan',
        'qty_disetujui',
        'harga_satuan',
        'subtotal',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
