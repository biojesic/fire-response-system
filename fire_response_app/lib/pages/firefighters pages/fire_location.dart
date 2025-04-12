import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';

class FireLocation extends StatefulWidget {
  final LatLng incidentLocation;

  const FireLocation({super.key, required this.incidentLocation});

  @override
  _FireLocationState createState() => _FireLocationState();
}

class _FireLocationState extends State<FireLocation> {
  LatLng? firefighterLocation;
  final MapController _mapController = MapController();

  @override
  void initState() {
    super.initState();
    _loadCurrentLocation();
  }

  Future<void> _loadCurrentLocation() async {
    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      setState(() {
        firefighterLocation = LatLng(position.latitude, position.longitude);
      });

      // Center the map between the firefighter & incident location
      _mapController.move(
        _getMapCenter(widget.incidentLocation, firefighterLocation!),
        13.0,
      );
    } catch (e) {
      print("Error getting location: $e");
    }
  }

  LatLng _getMapCenter(LatLng loc1, LatLng loc2) {
    return LatLng(
      (loc1.latitude + loc2.latitude) / 2,
      (loc1.longitude + loc2.longitude) / 2,
    );
  }

  Future<Position> getCurrentLocation() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw Exception("GPS is disabled.");
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.deniedForever) {
        throw Exception("Location permission permanently denied.");
      }
    }

    return await Geolocator.getCurrentPosition(
      desiredAccuracy: LocationAccuracy.high,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Fire Responder Tracker")),
      body: FlutterMap(
        mapController: _mapController,
        options: MapOptions(
          initialCenter: widget.incidentLocation, // Updated
          initialZoom: 13.0, // Updated
        ),
        children: [
          TileLayer(
            urlTemplate: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            subdomains: ['a', 'b', 'c'],
          ),
          MarkerLayer(
            markers: [
              Marker(
                point: widget.incidentLocation,
                width: 50,
                height: 50,
                child: Icon(
                  Icons.local_fire_department,
                  color: Colors.red,
                  size: 40,
                ), // Updated
              ),
              if (firefighterLocation != null)
                Marker(
                  point: firefighterLocation!,
                  width: 50,
                  height: 50,
                  child: Icon(
                    Icons.person_pin_circle,
                    color: Colors.blue,
                    size: 40,
                  ), // Updated
                ),
            ],
          ),
        ],
      ),
    );
  }
}
