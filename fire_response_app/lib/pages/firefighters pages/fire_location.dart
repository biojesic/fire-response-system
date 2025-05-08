import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
import 'package:latlong2/latlong.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/provider/assigned_incident_provider.dart';
import 'package:fire_response_app/api.dart';

class FireLocation extends StatefulWidget {
  @override
  _FireLocationState createState() => _FireLocationState();
}

class _FireLocationState extends State<FireLocation> {
  static const String api = API.baseUrl;
  LatLng? firefighterLocation;
  Set<Marker> _markers = {};
  Polyline? _routePolyline;
  String? eta; // Store ETA as a string
  MapController _mapController =
      MapController(); // MapController to control camera view

  @override
  void initState() {
    super.initState();
    _loadCurrentLocation();
  }

  // Load current location of the firefighter
  Future<void> _loadCurrentLocation() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    LocationPermission permission = await Geolocator.checkPermission();

    if (!serviceEnabled || permission == LocationPermission.deniedForever) {
      return; // Handle location permission issues
    }

    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }

    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      setState(() {
        firefighterLocation = LatLng(position.latitude, position.longitude);
        _markers.clear();
        _markers.add(
          Marker(
            point: firefighterLocation!,
            child: Image.asset(
              'assets/firestation-icon.webp',
              width: 200.0, // Adjust the size of the image
              height: 200.0,
            ),
          ),
        );
      });

      if (firefighterLocation != null) {
        _getRouteAndETA(firefighterLocation!);
      }
    } catch (e) {
      print("Error getting location: $e");
    }
  }

  Future<void> _getRouteAndETA(LatLng origin) async {
    final assignedIncidentProvider = Provider.of<AssignedIncidentProvider>(
      context,
      listen: false,
    );

    final assignedIncident = assignedIncidentProvider.assignedIncident;

    if (assignedIncident == null) {
      print("No assigned incident found.");
      return;
    }

    LatLng incidentLocation = LatLng(
      assignedIncident.latitude,
      assignedIncident.longitude,
    );

    // Log the origin and destination coordinates to verify the values
    print(
      "Origin Coordinates: Latitude = ${origin.latitude}, Longitude = ${origin.longitude}",
    );
    print(
      "Destination Coordinates: Latitude = ${incidentLocation.latitude}, Longitude = ${incidentLocation.longitude}",
    );

    setState(() {
      _markers.add(
        Marker(
          point: incidentLocation,
          child: Image.asset(
            'assets/fire-icon.png',
            width: 220.0,
            height: 220.0,
          ),
        ),
      );
    });

    _mapController.move(incidentLocation, 15.0);

    final url = '$api/get-route-eta';

    try {
      final response = await http.post(
        Uri.parse(url),
        body: json.encode({
          'origin_lat': origin.latitude,
          'origin_lng': origin.longitude,
          'destination_lat': incidentLocation.latitude,
          'destination_lng': incidentLocation.longitude,
        }),
        headers: {'Content-Type': 'application/json'},
      );

      print('Request Body:');
      print(
        json.encode({
          'origin_lat': origin.latitude,
          'origin_lng': origin.longitude,
          'destination_lat': incidentLocation.latitude,
          'destination_lng': incidentLocation.longitude,
        }),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        var etaString = data['eta'];

        // Log the ETA string
        print('ETA: $etaString');

        setState(() {
          eta = etaString; // Store the ETA as a string
        });
      } else {
        print('Error: ${response.statusCode}');
      }
    } catch (e) {
      print('Error: $e');
    }
  }

  // Decode the polyline for route
  List<LatLng> _decodePolyline(String polyline) {
    List<LatLng> points = [];
    int index = 0;
    int len = polyline.length;
    int lat = 0;
    int lng = 0;

    while (index < len) {
      int shift = 0;
      int result = 0;
      int byte;

      do {
        byte = polyline.codeUnitAt(index++) - 63;
        result |= (byte & 0x1f) << shift;
        shift += 5;
      } while (byte >= 0x20);

      int deltaLat = ((result & 1) != 0 ? ~(result >> 1) : (result >> 1));
      lat += deltaLat;

      shift = 0;
      result = 0;

      do {
        byte = polyline.codeUnitAt(index++) - 63;
        result |= (byte & 0x1f) << shift;
        shift += 5;
      } while (byte >= 0x20);

      int deltaLng = ((result & 1) != 0 ? ~(result >> 1) : (result >> 1));
      lng += deltaLng;

      points.add(LatLng(lat / 1E5, lng / 1E5));
    }

    return points;
  }

  // Calculate zoom level based on distance
  double _calculateZoomLevel(LatLng origin, LatLng destination) {
    double distance = Geolocator.distanceBetween(
      origin.latitude,
      origin.longitude,
      destination.latitude,
      destination.longitude,
    );
    if (distance < 1000) {
      return 15.0; // More zoomed in
    } else if (distance < 5000) {
      return 12.0;
    } else {
      return 10.0; // Less zoomed in
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Fire Responder Tracker")),
      body: Stack(
        children: [
          FlutterMap(
            mapController: _mapController, // Assign the map controller
            options: MapOptions(
              initialCenter: firefighterLocation ?? LatLng(14.5995, 120.9842),
              initialZoom: _calculateZoomLevel(
                firefighterLocation ?? LatLng(14.5995, 120.9842),
                LatLng(14.5995, 120.9842),
              ),
            ),
            children: [
              TileLayer(
                urlTemplate:
                    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                subdomains: ['a', 'b', 'c'],
              ),
              MarkerLayer(markers: _markers.toList()),
              if (_routePolyline != null)
                PolylineLayer(polylines: [_routePolyline!]),
            ],
          ),
          if (eta != null)
            Positioned(
              bottom: 20, // Position the card towards the bottom
              right: 20, // Position it to the right side
              child: Card(
                color: Colors.blueAccent,
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Text(
                    'ETA: $eta',
                    style: TextStyle(fontSize: 16, color: Colors.white),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
