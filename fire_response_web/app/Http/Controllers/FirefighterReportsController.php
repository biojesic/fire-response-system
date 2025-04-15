<?php

namespace App\Http\Controllers;

use App\Models\FirefighterReports;
use Illuminate\Http\Request;

class FirefighterReportsController extends Controller
{
    /**
     * Display a listing of the firefighter reports.
     */
    public function index()
    {
        // Fetch all firefighter reports with relationships
        return FirefighterReports::with(['fireReport', 'firefighter'])->get();
    }

    /**
     * Store a newly created firefighter report in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'fireReportId' => 'required|exists:fire_reports,id', // Ensure the fire_report exists
            'fireFighterId' => 'required|exists:firefighters,id', // Ensure the firefighter exists
            'reportType' => 'required|in:Investigation Report,Incident Report,Spot Investigation Report',
            'details' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png', // Optional attachment (PDF or image)
        ]);

        // Store the new firefighter report
        $firefighterReport = FirefighterReports::create($request->all());

        return response()->json($firefighterReport, 201); // Return the newly created report
    }

    /**
     * Display the specified firefighter report.
     */
    public function show($id)
    {
        // Fetch a firefighter report by ID with relationships
        return FirefighterReports::with(['fireReport', 'firefighter'])->findOrFail($id);
    }

    /**
     * Update the specified firefighter report in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'fireReportId' => 'required|exists:fire_reports,id',
            'fireFighterId' => 'required|exists:firefighters,id',
            'reportType' => 'required|in:Investigation Report,Incident Report,Spot Investigation Report',
            'details' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        // Find the firefighter report by ID
        $firefighterReport = FirefighterReports::findOrFail($id);

        // Update the firefighter report
        $firefighterReport->update($request->all());

        return response()->json($firefighterReport); // Return the updated report
    }

    /**
     * Remove the specified firefighter report from storage.
     */
    public function destroy($id)
    {
        // Find the firefighter report by ID
        $firefighterReport = FirefighterReports::findOrFail($id);

        // Delete the firefighter report
        $firefighterReport->delete();

        return response()->json(['message' => 'Firefighter report deleted successfully']);
    }
}
