<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    /**
     * Manage wilayahs (Owner only) — list, create, assign admins.
     */
    public function index()
    {
        $wilayahs = Wilayah::with('users')->get();
        $admins = User::where('role', 'admin')->get();

        return view('wilayah.index', compact('wilayahs', 'admins'));
    }

    /**
     * Store a new wilayah.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_wilayah' => 'required|string|max:255|unique:wilayahs,nama_wilayah',
        ]);

        Wilayah::create(['nama_wilayah' => $request->nama_wilayah]);

        return redirect()->route('wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    /**
     * Delete a wilayah.
     */
    public function destroy($id)
    {
        $wilayah = Wilayah::findOrFail($id);
        $wilayah->delete();

        return redirect()->route('wilayah.index')->with('success', 'Wilayah berhasil dihapus.');
    }

    /**
     * Assign admin(s) to a wilayah.
     */
    public function assignAdmins(Request $request, $id)
    {
        $wilayah = Wilayah::findOrFail($id);
        
        $request->validate([
            'admin_ids' => 'nullable|array',
            'admin_ids.*' => 'exists:users,id',
        ]);

        // Sync: replace all admin assignments for this wilayah
        $wilayah->users()->sync($request->input('admin_ids', []));

        return redirect()->route('wilayah.index')->with('success', "Admin untuk wilayah {$wilayah->nama_wilayah} berhasil diperbarui.");
    }

    /**
     * Switch active wilayah (Finance/Owner only).
     */
    public function switchWilayah($wilayahId)
    {
        if ($wilayahId === 'all') {
            session()->forget('active_wilayah_id');
        } else {
            $wilayah = Wilayah::findOrFail($wilayahId);
            session(['active_wilayah_id' => $wilayah->id]);
        }

        return back();
    }
}
