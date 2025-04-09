import 'package:fire_response_app/pages/firefighters%20pages/fire_location.dart';
import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:latlong2/latlong.dart';

class AssignedIncidentDetails extends StatefulWidget {
  const AssignedIncidentDetails({super.key});

  @override
  State<AssignedIncidentDetails> createState() =>
      _AssignedIncidentDetailsState();
}

class _AssignedIncidentDetailsState extends State<AssignedIncidentDetails> {
  String incidentAddress = "Brgy. Langkaan 1"; // Example address
  LatLng? incidentLatLng;

  @override
  void initState() {
    super.initState();
    _getLatLngFromAddress();
  }

  Future<void> _getLatLngFromAddress() async {
    final String url =
        "https://nominatim.openstreetmap.org/search?format=json&q=$incidentAddress";

    try {
      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        final List data = json.decode(response.body);

        if (data.isNotEmpty) {
          double lat = double.parse(data[0]["lat"]);
          double lon = double.parse(data[0]["lon"]);

          setState(() {
            incidentLatLng = LatLng(lat, lon);
          });

          print("Incident Location: $lat, $lon");
        } else {
          print("No coordinates found for this address.");
        }
      } else {
        print("Error fetching coordinates: ${response.statusCode}");
      }
    } catch (e) {
      print("Error: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Incident Details')),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(14.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                'Fire Location',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w400),
              ),
              SizedBox(height: 5),
              Text(
                incidentAddress,
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
              ),
              SizedBox(height: 5),
              InkWell(
                onTap:
                    incidentLatLng == null
                        ? null
                        : () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder:
                                  (context) => FireLocation(
                                    incidentLocation: incidentLatLng!,
                                  ),
                            ),
                          );
                        },
                child: Text(
                  'Open Location',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    decoration: TextDecoration.underline,
                    color: incidentLatLng == null ? Colors.grey : Colors.blue,
                  ),
                ),
              ),
              SizedBox(height: 10),
              Text(
                'Landmark',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w400),
              ),
              SizedBox(height: 5),
              Text(
                'Near Gas Station',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
              ),
              SizedBox(height: 10),
              Text(
                'Time Reported',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w400),
              ),
              SizedBox(height: 5),
              Text(
                '9:00 pm',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
              ),
              SizedBox(height: 10),
              Text(
                'Incident Type',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w400),
              ),
              SizedBox(height: 5),
              Text(
                'Residential',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
              ),
              SizedBox(height: 10),
              Text(
                'Units Assigned',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w400),
              ),
              SizedBox(height: 5),
              Text(
                'Team Alpha',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
              ),
              SizedBox(height: 30),
              SizedBox(
                width: 340,
                height: 55,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red[400],
                  ),
                  onPressed: () {},
                  child: Text(
                    'Request Additional Support',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 17,
                    ),
                  ),
                ),
              ),
              SizedBox(height: 40),
              Divider(),
              SizedBox(height: 30),
              SizedBox(
                height: 40,
                width: 200,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.green[600],
                  ),
                  onPressed: () {},
                  child: Text(
                    'Mark as Contained',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
