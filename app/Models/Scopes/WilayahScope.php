<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WilayahScope implements Scope
{
    /**
     * Apply wilayah-based data isolation.
     *
     * Admin: filter to only their assigned wilayah(s).
     * Finance/Owner: filter by session 'active_wilayah_id' if set,
     *                otherwise show all data.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin only sees data from their currently active wilayah
            if (session()->has('active_wilayah_id')) {
                $builder->where('wilayah_id', session('active_wilayah_id'));
            } else {
                // No active wilayah set — show nothing until middleware sets one
                $builder->whereRaw('1 = 0');
            }
        } elseif (in_array($user->role, ['finance', 'owner'])) {
            // Finance/Owner: filter by active wilayah if selected
            if (session()->has('active_wilayah_id')) {
                $builder->where('wilayah_id', session('active_wilayah_id'));
            }
            // If no wilayah selected → show all data (no filter)
        }
    }
}
