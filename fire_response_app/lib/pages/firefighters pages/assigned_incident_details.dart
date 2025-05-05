import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:fire_response_app/pages/firefighters%20pages/fire_location.dart';
import 'package:fire_response_app/provider/assigned_incident_provider.dart'; // Import the AssignedIncidentProvider

class AssignedIncidentDetails extends StatefulWidget {
  const AssignedIncidentDetails({super.key});

  @override
  State<AssignedIncidentDetails> createState() =>
      _AssignedIncidentDetailsState();
}

class _AssignedIncidentDetailsState extends State<AssignedIncidentDetails> {
  LatLng? incidentLatLng; // Variable to hold the incident location
  late String incidentAddress;

  @override
  void initState() {
    super.initState();
    // Fetch assigned incident directly from the AssignedIncidentProvider
    Provider.of<AssignedIncidentProvider>(
      context,
      listen: false,
    ).fetchAssignedIncident(context);
  }

  Future<void> _getLatLngFromAddress(String address) async {
    if (address.isEmpty) {
      print("Error: Address is empty.");
      return;
    }

    final String encodedAddress = Uri.encodeComponent(address);
    final String url =
        "https://nominatim.openstreetmap.org/search?format=json&q=$encodedAddress";

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
    return Consumer<AssignedIncidentProvider>(
      builder: (context, assignedIncidentProvider, child) {
        final assignedIncident = assignedIncidentProvider.assignedIncident;

        if (assignedIncident == null) {
          return const Center(child: CircularProgressIndicator());
        }

        // Set the assignedIncident.location here to incidentAddress
        incidentAddress = assignedIncident.location ?? '';

        // Fetch coordinates only when the address is available and incidentLatLng is not set
        if (incidentAddress.isNotEmpty && incidentLatLng == null) {
          _getLatLngFromAddress(incidentAddress);
        }

        return Scaffold(
          appBar: AppBar(title: const Text('Assigned Incident Details')),
          body: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Location: ${assignedIncident.location ?? 'Location not available'}",
                  style: const TextStyle(fontSize: 18),
                ),
                const SizedBox(height: 10),
                InkWell(
                  onTap:
                      incidentLatLng == null
                          ? null
                          : () {
                            if (incidentLatLng != null) {
                              print(
                                "Navigating to map with coordinates: $incidentLatLng",
                              );
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder:
                                      (context) =>
                                          FireLocation(), // No parameters
                                ),
                              );
                            } else {
                              print("LatLng is null, cannot navigate to map.");
                            }
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
                const SizedBox(height: 10),
                Text(
                  "Landmark: ${assignedIncident.landmark ?? 'Landmark not available'}",
                  style: const TextStyle(fontSize: 18),
                ),
                const SizedBox(height: 10),
                Text(
                  "Time Reported: ${assignedIncident.timeReported ?? 'Time not available'}",
                  style: const TextStyle(fontSize: 18),
                ),
                const SizedBox(height: 10),
                Text(
                  "Assigned Team/s: ${assignedIncident.teamName ?? 'No teams assigned'}",
                  style: const TextStyle(fontSize: 18),
                ),
                const SizedBox(height: 10),
                Text(
                  "Team Leader/s: ${assignedIncident.teamLeader ?? 'No leaders assigned'}",
                  style: const TextStyle(fontSize: 18),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
