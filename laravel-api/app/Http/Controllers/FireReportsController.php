<?php

namespace App\Http\Controllers;

use App\Models\FireReports;
use App\Models\FireStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class FireReportsController extends Controller
{
    private function getCoordinatesFromAddress($address)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;

        Log::info("Geocoding API request: " . $url);

        try {
            $response = Http::get($url);
            $data = $response->json();

            if ($data['status'] == 'OK') {
                return [
                    'latitude' => $data['results'][0]['geometry']['location']['lat'],
                    'longitude' => $data['results'][0]['geometry']['location']['lng']
                ];
            }
        } catch (\Exception $e) {
            Log::error("Geocoding API request error: " . $e->getMessage());
        }
        return null;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function findNearestFireStation($latitude, $longitude)
    {
        $fireStations = FireStation::all();
        $nearestStation = null;
        $shortestDistance = PHP_INT_MAX;

        foreach ($fireStations as $fireStation) {
            $distance = $this->calculateDistance($latitude, $longitude, $fireStation->latitude, $fireStation->longitude);
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestStation = $fireStation;
            }
        }
        return $nearestStation;
    }

    public function quickReport(Request $request)
    {
        $validatedData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'nullable|string',
            'landmark' => 'nullable|string',
            'description' => 'nullable|string',
            'contact_info' => 'nullable|string',
        ]);

        $fireReport = new FireReports();
        if (Auth::check()) {
            $fireReport->reported_by = Auth::id(); // Authenticated user
        } else {
            $fireReport->contact_info = $request->contact_info; // Guest user
        }

        $fireReport->latitude = $request->latitude;
        $fireReport->longitude = $request->longitude;
        $fireReport->location = $request->location ?? 'Unknown Location';
        $fireReport->landmark = $request->landmark ?? 'No Landmark';
        $fireReport->description = $request->description ?? 'Emergency Reported via Quick Button';
        $fireReport->status = 'Pending';
        $fireReport->save();

        return response()->json(['message' => 'Emergency report submitted successfully!']);
    }

    public function getUserReports($reported_by)
    {
        $user = Auth::user(); // Get the currently authenticated user

        if ($user->id != $reported_by) {
            return response()->json(['message' => 'Unauthorized'], 403); // Unauthorized access
        }

        // Fetch the fire reports where the reportedBy (user_id) matches the current user
        $reports = FireReports::where('reported_by', $user->id)->get();

        // Return the reports as JSON
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'location' => 'required',
            'landmark' => 'nullable|max:255',
            'description' => 'nullable',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_info' => 'nullable|string|max:255', // For guests
        ]);

        // Ensure latitude & longitude are set
        $latitude = $fields['latitude'] ?? null;
        $longitude = $fields['longitude'] ?? null;

        if (!$latitude || !$longitude) {
            $coordinates = $this->getCoordinatesFromAddress($fields['location']);
            if ($coordinates) {
                $latitude = $coordinates['latitude'];
                $longitude = $coordinates['longitude'];
            } else {
                return response()->json(['error' => 'Unable to retrieve coordinates.'], 400);
            }
        }

        // Find the nearest fire station
        $nearestFireStation = $this->findNearestFireStation($latitude, $longitude);
        if (!$nearestFireStation) {
            return response()->json(['error' => 'No nearby fire station found'], 400);
        }

        // Assign Fire Station & Coordinates
        $fields['latitude'] = $latitude;
        $fields['longitude'] = $longitude;
        $fields['fireStationId'] = $nearestFireStation->id;

        // Determine if request comes from an authenticated user
        if ($request->user()) {
            $fields['reported_by'] = $request->user()->id;
            $fireReport = $request->user()->fireReports()->create($fields);
        } else {
            // Guest user, store manually
            $fireReport = FireReports::create($fields);
        }

        return response()->json($fireReport, 201);
    }

    public function update(Request $request, $id)
    {
        $fireReport = FireReports::find($id);
        if (!$fireReport) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }

        Gate::authorize('update', $fireReport);

        $fields = $request->validate([
            'reported_by' => 'required|exists:users,id',
            'location' => 'required',
            'landmark' => 'required|max:255',
            'description' => 'nullable',
            'status' => 'required|in:Ongoing,Resolved',
        ]);

        $coordinates = $this->getCoordinatesFromAddress($fields['location']);
        if (!$coordinates) {
            return response()->json(['error' => 'Unable to retrieve coordinates.'], 400);
        }

        $nearestFireStation = $this->findNearestFireStation($coordinates['latitude'], $coordinates['longitude']);
        if (!$nearestFireStation) {
            return response()->json(['error' => 'No fire stations found nearby.'], 400);
        }

        $fireReport->update([
            'reported_by' => $fields['reported_by'],
            'location' => $fields['location'],
            'landmark' => $fields['landmark'],
            'description' => $fields['description'],
            'status' => $fields['status'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'fireStationId' => $nearestFireStation->id,
        ]);

        return response()->json($fireReport, 200);
    }

    public function destroy($id)
    {
        $fireReport = FireReports::find($id);
        if (!$fireReport) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }

        $fireReport->delete();

        return response()->json(['message' => 'Successfully deleted']);
    }
}
