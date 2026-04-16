<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWilayahContext
{
    /**
     * Auto-set active wilayah for Admin users on each request.
     * For Finance/Owner, leave the session as-is.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'admin') {
                // If admin has no active wilayah set, pick their first assigned one
                if (!session()->has('active_wilayah_id')) {
                    $firstWilayah = \DB::table('user_wilayahs')
                        ->where('user_id', $user->id)
                        ->value('wilayah_id');

                    if ($firstWilayah) {
                        session(['active_wilayah_id' => $firstWilayah]);
                    }
                }
            }
        }

        return $next($request);
    }
}
