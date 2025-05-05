<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class RouteApiController extends Controller
{
    public function getRouteAndETA(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'destination_lat' => 'required|numeric',
            'destination_lng' => 'required|numeric',
        ]);

        // Google API Key
        // $googleApiKey = env('GOOGLE_MAPS_API_KEY');

        // Google Maps Directions API URL
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$request->origin_lat},{$request->origin_lng}&destination={$request->destination_lat},{$request->destination_lng}&key=AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4";
        Log::info('Constructed Google API URL: ' . $url);

        try {
            // Create a new Guzzle client
            $client = new Client();
            
            // Send a GET request to the Google Directions API
            $response = $client->get($url);
            
            // Decode the response body
            $data = json_decode($response->getBody(), true);

            if (!empty($data['routes'])) {
                $route = $data['routes'][0]['legs'][0];

                // Extract ETA and polyline points
                $eta = $route['duration']['text']; // ETA in human-readable form
                $polyline = $data['routes'][0]['overview_polyline']['points']; // Encoded polyline string

                // Return the response with the ETA and polyline
                return response()->json([
                    'eta' => $eta,
                    'polyline' => $polyline,
                ]);
            } else {
                return response()->json(['error' => 'No routes found'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch directions: ' . $e->getMessage()], 500);
        }
    }

    public function getETAForCivilians(Request $request)
{
    // Fetch the ETA (this could be from the database or calculated on the fly)
    $eta = "2 mins"; // Example ETA, this should be dynamic

    return response()->json([
        'eta' => $eta,
    ]);
}

public function storeETA(Request $request)
{
    // Validate the request
    $request->validate([
        'eta' => 'required|string',
        'firefighter_id' => 'required|integer', // Or whatever identifier you use
    ]);

    // Store the ETA for the firefighter
    $firefighter = Firefighter::find($request->firefighter_id);
    $firefighter->eta = $request->eta;
    $firefighter->save();

    return response()->json(['message' => 'ETA updated successfully']);
}

}
