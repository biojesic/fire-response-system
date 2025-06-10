<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CityAndMunicipality;
use App\Http\Controllers\Controller;

class LGUWebController extends Controller
{
    public function index()
    {
        $lgus = CityAndMunicipality::orderBy('name')->paginate(15);
        return view('superadmin_pages.lgu.index', compact('lgus'));
    }

    public function create()
    {
        return view('admin.lgus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:cities_and_municipalities,name',
            'type' => 'required|in:City,Municipality',
            'province' => 'required|string',
        ]);

        CityAndMunicipality::create($validated);

        return redirect()->route('admin.lgus.index')
            ->with('success', 'LGU created successfully.');
    }

    public function show($id)
    {
        $lgu = CityAndMunicipality::findOrFail($id);
        return view('admin.lgu.show', compact('lgu'));
    }

    public function edit($id)
    {
        $lgu = CityAndMunicipality::findOrFail($id);
        return view('superadmin_pages.lgu.edit', compact('lgu'));
    }

    public function update(Request $request, $id)
    {
        $lgu = CityAndMunicipality::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:cities_and_municipalities,name,' . $lgu->id,
            'type' => 'required|in:City,Municipality',
            'province' => 'required|string',
        ]);

        $lgu->update($validated);

        return redirect()->route('admin.lgus.index')
            ->with('success', 'LGU updated successfully.');
    }

    public function destroy($id)
    {
        $lgu = CityAndMunicipality::findOrFail($id);
        $lgu->delete();

        return redirect()->route('admin.lgus.index')
            ->with('success', 'LGU deleted successfully.');
    }
}
