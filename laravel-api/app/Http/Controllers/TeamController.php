<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        return Team::all(); // Fetch all teams
    }

    public function store(Request $request)
    {
        $request->validate([
            'teamName' => 'required|unique:teams',
            'fireStationId' => 'required|exists:fire_station,id',
            'assignedFireIncident' => 'nullable|exists:fire_reports,id',
            'firetruckName' => 'nullable|string',
            'truckEquipmentList' => 'nullable|array',
            // 'status' => 'required|in:standby,on_response',
        ]);

        return Team::create($request->all());
    }

    public function show($id)
    {
        return Team::findOrFail($id); // Fetch a specific team by ID
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'teamName' => 'required|unique:teams,teamName,' . $id,
            'fireStationId' => 'required|exists:fire_station,id',
            'assignedFireIncident' => 'nullable|exists:fire_reports,id',
            'firetruckName' => 'nullable|string',
            'truckEquipmentList' => 'nullable|array',
            'status' => 'required|in:standby,on_response',
        ]);

        $team = Team::findOrFail($id);
        $team->update($request->all());

        return $team;
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return response()->json(['message' => 'Team deleted successfully']);
    }
}
