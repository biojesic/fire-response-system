<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FirefighterPosition;

class FirefighterPositionController extends Controller
{
    // 📌 GET: Fetch all positions
    public function index()
    {
        return response()->json(FirefighterPosition::all());
    }

    // 📌 POST: Add a new position
    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|unique:firefighter_positions|max:255',
        ]);

        $position = FirefighterPosition::create(['position_name' => $request->position_name]);

        return response()->json(['message' => 'Position added successfully!', 'position' => $position], 201);
    }

    // 📌 PUT: Update position
    public function update(Request $request, $id)
    {
        $position = FirefighterPosition::find($id);
        if (!$position) {
            return response()->json(['error' => 'Position not found'], 404);
        }

        $request->validate([
            'position_name' => 'required|unique:firefighter_positions|max:255',
        ]);

        $position->update(['position_name' => $request->position_name]);

        return response()->json(['message' => 'Position updated successfully!', 'position' => $position]);
    }

    // 📌 DELETE: Remove a position
    public function destroy($id)
    {
        $position = FirefighterPosition::find($id);
        if (!$position) {
            return response()->json(['error' => 'Position not found'], 404);
        }

        $position->delete();
        return response()->json(['message' => 'Position deleted successfully!']);
    }
}
