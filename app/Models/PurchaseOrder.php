<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'no_po',
        'admin_id',
        'wilayah_id',
        'tipe_po',
        'status',
        'catatan_admin',
        'catatan_finance',
        'catatan_owner',
        'total_pengajuan',
        'total_disetujui',
        'approved_by_finance_at',
        'approved_by_owner_at',
        'finalized_at',
    ];

    protected $casts = [
        'approved_by_finance_at' => 'datetime',
        'approved_by_owner_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\WilayahScope);
    }


    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function items()
    {
        return $this->hasMany(PoItem::class);
    }

    /**
     * Generate a unique PO number: PO-YYYYMMDD-XXX
     */
    public static function generateNoPo(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'PO-' . $today . '-';

        $lastPo = static::where('no_po', 'like', $prefix . '%')
            ->orderBy('no_po', 'desc')
            ->first();

        if ($lastPo) {
            $lastNumber = (int) substr($lastPo->no_po, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate totals based on items.
     */
    public function recalculateTotals(): void
    {
        $this->total_pengajuan = $this->items->sum(function ($item) {
            return $item->qty_pengajuan * $item->harga_satuan;
        });

        $this->total_disetujui = $this->items->sum(function ($item) {
            return ($item->qty_disetujui ?? 0) * $item->harga_satuan;
        });

        $this->save();
    }

    /**
     * Status label with badge class for display.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending_finance' => ['label' => 'Menunggu Finance', 'class' => 'bg-warning text-dark'],
            'pending_owner' => ['label' => 'Menunggu Owner', 'class' => 'bg-info text-dark'],
            'waiting_final_adjustment' => ['label' => 'Finalisasi Finance', 'class' => 'bg-secondary'],
            'approved' => ['label' => 'Disetujui', 'class' => 'bg-success'],
            'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-dark'],
        };
    }
}
