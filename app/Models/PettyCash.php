<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    protected $table = 'petty_cash';

    protected $fillable = [
        'wilayah_id',
        'admin_id',
        'kategori',
        'nominal',
        'keterangan',
        'bukti_nota',
        'status',
        'catatan_finance',
        'approved_by',
        'approved_at',
        'tanggal',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'tanggal' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\WilayahScope);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => ['class' => 'bg-warning text-dark', 'label' => 'Pending'],
            'approved' => ['class' => 'bg-success', 'label' => 'Approved'],
            'rejected' => ['class' => 'bg-danger', 'label' => 'Rejected'],
            default => ['class' => 'bg-secondary', 'label' => $this->status],
        };
    }

    public const KATEGORI_LIST = [
        'Kebersihan',
        'ATK',
        'Transport',
        'Konsumsi',
        'Perbaikan',
        'Lainnya',
    ];
}
