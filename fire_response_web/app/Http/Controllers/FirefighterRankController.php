<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirefighterRankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ranks = FirefighterRank::all();
        // return view('admin.firefighterRanks.index', compact('ranks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('admin.firefighterRanks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'rank_name' => 'required|string|max:255|unique:firefighter_ranks', // Ensure unique rank names
        ]);

        // Create a new FirefighterRank record
        FirefighterRank::create([
            'rank_name' => $validated['rank_name'],
        ]);

        // return redirect()->route('admin.firefighterRanks.index')->with('success', 'Rank created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'rank_name' => 'required|string|max:255|unique:firefighter_ranks,rank_name,' . $id, // Ensure unique rank name, excluding current rank
        ]);

        // Find the rank and update it
        $rank = FirefighterRank::findOrFail($id);
        $rank->update([
            'rank_name' => $validated['rank_name'],
        ]);

        // Redirect back with a success message
        // return redirect()->route('admin.firefighterRanks.index')->with('success', 'Rank updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rank = FirefighterRank::findOrFail($id);
        $rank->delete(); // Delete the rank

        // return redirect()->route('admin.firefighterRanks.index')->with('success', 'Rank deleted successfully!');
    }
}
