<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayahs';

    protected $fillable = ['nama_wilayah'];

    /**
     * Users assigned to this wilayah (admin users).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_wilayahs', 'wilayah_id', 'user_id');
    }
}
