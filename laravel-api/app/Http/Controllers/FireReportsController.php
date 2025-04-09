<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FireStation;
use App\Models\FireReports;
use App\Models\FireStation as ModelsFireStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class FireReportsController extends Controller
{

    private function getCoordinatesFromAddress($address)
    {
        $client = new Client();
        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;

        Log::info("Geocoding API request: " . $url);

        try {
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

            Log::info("Geocoding API response: " . json_encode($data));

            if ($data['status'] == 'OK') {
                $latitude = $data['results'][0]['geometry']['location']['lat'];
                $longitude = $data['results'][0]['geometry']['location']['lng'];

                Log::info("Geocoding successful: lat=$latitude, lng=$longitude");

                return ['latitude' => $latitude, 'longitude' => $longitude];
            } else {
                Log::error("Geocoding API failed: " . json_encode($data));
            }
        } catch (\Exception $e) {
            Log::error("Geocoding API request error: " . $e->getMessage());
        }

        return null;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distance in kilometers
        return $earthRadius * $c;
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

    // Method to find the nearest fire station to the given coordinates
    private function findNearestFireStation($latitude, $longitude)
    {
        $fireStations = ModelsFireStation::all();
        $nearestStation = null;
        $shortestDistance = PHP_INT_MAX; // Start with an infinitely large distance

        Log::info("Finding nearest fire station for coordinates: lat=$latitude, lng=$longitude");

        foreach ($fireStations as $fireStation) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $fireStation->latitude,
                $fireStation->longitude
            );

            // Log the distance for each fire station
            Log::info("Fire Station: {$fireStation->name}, Distance: $distance km");

            // If this distance is shorter than the current shortest, update
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestStation = $fireStation;
            }
        }

        if ($nearestStation) {
            Log::info("Nearest fire station found: {$nearestStation->name}, Distance: $shortestDistance km");
        } else {
            Log::error("No nearest fire station found.");
        }

        return $nearestStation; // Return the nearest fire station
    }

    public function store(Request $request)
    {
        // Validate the required fields
        $fields = $request->validate([
            'location' => 'required',
            'landmark' => 'required|max:255',
            'description' => 'nullable',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Get the coordinates from the request or geocode the address
        $latitude = $fields['latitude'] ?? null;
        $longitude = $fields['longitude'] ?? null;

        if (!$latitude || !$longitude) {
            Log::info("Geocoding started for location: " . $fields['location']);
            $coordinates = $this->getCoordinatesFromAddress($fields['location']);

            if ($coordinates) {
                $latitude = $coordinates['latitude'];
                $longitude = $coordinates['longitude'];
                Log::info("Geocoding successful: lat=$latitude, lng=$longitude");
            } else {
                Log::error("Geocoding failed for location: " . $fields['location']);
                return response()->json(['error' => 'Unable to retrieve coordinates for this location.'], 400);
            }
        }

        // Find the nearest fire station
        Log::info("Finding nearest fire station for lat=$latitude, lng=$longitude");
        $nearestFireStation = $this->findNearestFireStation($latitude, $longitude);

        if (!$nearestFireStation) {
            Log::error("No nearby fire station found for coordinates: lat=$latitude, lng=$longitude");
            return response()->json(['error' => 'No nearby fire station found'], 400);
        }

        // Set the latitude, longitude, and fire station ID
        $fields['latitude'] = $latitude;
        $fields['longitude'] = $longitude;
        $fields['fireStationId'] = $nearestFireStation->id;

        // Create the fire report
        $fireReport = $request->user()->FireReports()->create($fields);

        Log::info("Fire report created successfully with ID: {$fireReport->id}");

        return response()->json($fireReport, 201);
    }

    public function update(Request $request, $id)
    {
        $fireReports = FireReports::find($id);

        if (!$fireReports) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }

        Gate::authorize('update', $fireReports);

        $fields = $request->validate([
            'reported_by' => 'required|exists:users,id',
            'location' => 'required',
            'landmark' => 'required|max:255',
            'description' => 'nullable',
            'status' => 'required|in:Ongoing,Resolved',
        ]);

        // Get coordinates from address if not provided
        Log::info("Geocoding started for location: " . $fields['location']);
        $coordinates = $this->getCoordinatesFromAddress($fields['location']);

        if (!$coordinates) {
            return response()->json(['error' => 'Unable to retrieve coordinates for this location.'], 400);
        }

        // Find the nearest fire station
        Log::info("Finding nearest fire station for lat={$coordinates['latitude']}, lng={$coordinates['longitude']}");
        $nearestFireStation = $this->findNearestFireStation($coordinates['latitude'], $coordinates['longitude']);

        if (!$nearestFireStation) {
            return response()->json(['error' => 'No fire stations found nearby.'], 400);
        }

        // Update the fire report with coordinates and the nearest fire station
        $fireReports->update([
            'reported_by' => $fields['reported_by'],
            'location' => $fields['location'],
            'landmark' => $fields['landmark'],
            'description' => $fields['description'],
            'status' => $fields['status'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'fireStationId' => $nearestFireStation->id, // Update the fire station ID
        ]);

        Log::info("Fire report updated successfully with ID: {$fireReports->id}");

        return response()->json($fireReports, 200);
    }

    public function destroy(Request $request, $id)
    {
        $fireReports = FireReports::find($id);

        if (!$fireReports) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }

        $fireReports->delete($id);

        return ["msg" => "Successfully deleted"];
    }
}
