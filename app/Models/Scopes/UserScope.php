<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'admin') {
                $builder->where('user_id', $user->id);
            } elseif (in_array($user->role, ['finance', 'owner'])) {
                if (session()->has('view_admin_id')) {
                    $builder->where('user_id', session('view_admin_id'));
                }
            }
        }
    }
}
