<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminViewController extends Controller
{
    public function switchAdmin(Request $request, $userId)
    {
        if ($userId === 'all') {
            session()->forget('view_admin_id');
        } else {
            // Verify user exists and is admin
            $user = \App\Models\User::where('id', $userId)->where('role', 'admin')->first();
            if ($user && in_array(auth()->user()->role, ['finance', 'owner'])) {
                session(['view_admin_id' => $user->id]);
            }
        }
        
        return back();
    }
}
