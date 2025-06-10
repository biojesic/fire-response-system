<?php

namespace App\Http\Controllers;

use App\Models\CityAndMunicipality;
use Illuminate\Http\Request;

class LGUController extends Controller
{
    public function index()
    {
        $lgus = CityAndMunicipality::orderBy('name')->get();
        return response()->json($lgus);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:cities_and_municipalities,name',
            'type' => 'required|in:City,Municipality',
            'province' => 'required|string',
        ]);

        $lgu = CityAndMunicipality::create($validated);

        return response()->json([
            'message' => 'LGU created successfully',
            'lgu' => $lgu,
        ], 201);
    }

    public function show($id)
    {
        $lgu = CityAndMunicipality::with('barangays')->findOrFail($id);
        return response()->json($lgu);
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

        return response()->json([
            'message' => 'LGU updated successfully',
            'lgu' => $lgu,
        ]);
    }

    public function destroy($id)
    {
        $lgu = CityAndMunicipality::findOrFail($id);
        $lgu->delete();

        return response()->json([
            'message' => 'LGU deleted successfully',
        ]);
    }
}
