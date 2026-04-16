<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesPersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salesPersons = \App\Models\SalesPerson::all();
        return view('sales-persons.index', compact('salesPersons'));
    }

    public function create()
    {
        return view('sales-persons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        \App\Models\SalesPerson::create(array_merge($request->all(), [
            'user_id' => auth()->id(),
            'wilayah_id' => session('active_wilayah_id'),
        ]));


        return redirect()->route('sales-persons.index')
            ->with('success', 'Data sales berhasil ditambahkan.');
    }

    public function edit(\App\Models\SalesPerson $salesPerson)
    {
        return view('sales-persons.edit', compact('salesPerson'));
    }

    public function update(Request $request, \App\Models\SalesPerson $salesPerson)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $salesPerson->update($request->all());

        return redirect()->route('sales-persons.index')
            ->with('success', 'Data sales berhasil diperbarui.');
    }

    public function destroy(\App\Models\SalesPerson $salesPerson)
    {
        $salesPerson->delete();
        return redirect()->route('sales-persons.index')
            ->with('success', 'Data sales berhasil dihapus.');
    }
}
