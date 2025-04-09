<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireStation;
use GuzzleHttp\Client;

class FireStationController extends Controller
{

    private function getCoordinatesFromAddress($address)
    {
        $client = new Client();
        $apiKey = env('GOOGLE_MAPS_API_KEY');  // Get the API key from .env

        // Prepare the URL for Google Geocoding API
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;

        // Send a GET request to the Geocoding API
        $response = $client->get($url);

        // Decode the JSON response from the API
        $data = json_decode($response->getBody(), true);

        // Check if the API response is OK
        if ($data['status'] == 'OK') {
            // Extract latitude and longitude from the response
            $latitude = $data['results'][0]['geometry']['location']['lat'];
            $longitude = $data['results'][0]['geometry']['location']['lng'];

            // Return the coordinates as an array
            return ['latitude' => $latitude, 'longitude' => $longitude];
        }

        // If geocoding failed, return null
        return null;
    }

    public function index()
    {
        return FireStation::all();
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'firestationName' => 'required|unique:fire_station,firestationName',
            'firestationLocation' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'firestationContactNumber' => 'nullable|string',
        ]);

        // If latitude and longitude are not provided, use address to fetch coordinates
        $coordinates = null;
        if (!$validated['latitude'] || !$validated['longitude']) {
            $coordinates = $this->getCoordinatesFromAddress($validated['firestationLocation']);
        }

        if (!$coordinates && (!$validated['latitude'] || !$validated['longitude'])) {
            return response()->json(['error' => 'Unable to retrieve coordinates for this address.'], 400);
        }

        // If coordinates were fetched, use them; otherwise, use the provided ones
        $latitude = $coordinates['latitude'] ?? $validated['latitude'];
        $longitude = $coordinates['longitude'] ?? $validated['longitude'];

        // Create a new fire station record
        $fireStation = FireStation::create([
            'firestationName' => $validated['firestationName'],
            'firestationLocation' => $validated['firestationLocation'],
            'firestationContactNumber' => $validated['firestationContactNumber'],
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return response()->json($fireStation, 201);
    }


    public function show($id)
    {
        $fireStation = FireStation::find($id);

        if (!$fireStation) {
            return response()->json(['message' => 'Fire Station not found'], 404);
        }

        return response()->json($fireStation);
    }

    // public function show(FireStation $fireStation)
    // {
    //     if (!$fireStation) {
    //         return response()->json(['message' => 'Fire Station not found'], 404);
    //     }
    //     return response()->json($fireStation);
    // }


    public function update(Request $request, FireStation $fireStation)
    {
        $request->validate([
            'firestationName' => 'required|unique:fire_station,firestationName,' . $fireStation->id,
            'firestationLocation' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'firestationContactNumber' => 'nullable|string',
        ]);

        $fireStation->update($request->all());

        return $fireStation;
    }

    public function destroy($id)
    {
        $fireStation = FireStation::find($id);

        if (!$fireStation) {
            return response()->json(['message' => 'Fire Station not found'], 404);
        }

        $fireStation->delete();
        return response()->json(['message' => 'Fire Station deleted successfully']);
    }
}
