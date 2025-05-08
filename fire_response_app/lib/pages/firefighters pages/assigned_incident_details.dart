import 'dart:convert';
import 'package:fire_response_app/pages/firefighters%20pages/final_real_time_report.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_home.dart';
import 'package:fire_response_app/provider/firefighter_provider.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:fire_response_app/pages/firefighters%20pages/fire_location.dart';
import 'package:fire_response_app/provider/assigned_incident_provider.dart';

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
    final String apiKey = "AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4";
    final String url =
        "https://maps.googleapis.com/maps/api/geocode/json?address=$encodedAddress&key=$apiKey";

    try {
      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        if (data['status'] == 'OK' && data['results'].isNotEmpty) {
          final location = data['results'][0]['geometry']['location'];
          double lat = location['lat'];
          double lng = location['lng'];

          setState(() {
            incidentLatLng = LatLng(lat, lng);
          });

          print("Incident Location: $lat, $lng");
        } else {
          print(
            "No coordinates found for this address. Status: ${data['status']}",
          );
        }
      } else {
        print("Error fetching coordinates: ${response.statusCode}");
      }
    } catch (e) {
      print("Exception occurred: $e");
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
          appBar: AppBar(
            title: Text('Assigned Incident'),
            backgroundColor: Colors.transparent, // Transparent background
            elevation: 0, // No shadow
            leading: IconButton(
              icon: Icon(
                Icons.arrow_back_ios,
                color: Colors.black,
              ), // Back arrow
              onPressed: () {
                Navigator.pop(context); // Go back to the previous screen
              },
            ),
          ),
          body: Padding(
            padding: const EdgeInsets.fromLTRB(18, 30, 18, 40),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        "Location: ${assignedIncident.location ?? 'Location not available'}",
                        style: TextStyle(
                          fontSize: 18,
                          fontFamily: GoogleFonts.poppins().fontFamily,
                        ),
                      ),
                    ),
                    IconButton(
                      onPressed: () {
                        if (incidentLatLng != null) {
                          print(
                            "Navigating to map with coordinates: $incidentLatLng",
                          );
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder:
                                  (context) => FireLocation(), // No parameters
                            ),
                          );
                        } else {
                          print("LatLng is null, cannot navigate to map.");
                        }
                      },
                      icon: Icon(
                        Icons.map_outlined,
                        color:
                            incidentLatLng == null ? Colors.grey : Colors.blue,
                      ),
                    ),
                  ],
                ),
                // const SizedBox(height: 10),
                // InkWell(
                //   onTap:
                //       incidentLatLng == null
                //           ? null
                //           : () {
                //             if (incidentLatLng != null) {
                //               print(
                //                 "Navigating to map with coordinates: $incidentLatLng",
                //               );
                //               Navigator.push(
                //                 context,
                //                 MaterialPageRoute(
                //                   builder:
                //                       (context) =>
                //                           FireLocation(), // No parameters
                //                 ),
                //               );
                //             } else {
                //               print("LatLng is null, cannot navigate to map.");
                //             }
                //           },
                //   child: Text(
                //     'Open Location',
                //     style: TextStyle(
                //       fontFamily: GoogleFonts.poppins().fontFamily,
                //       fontSize: 18,
                //       fontWeight: FontWeight.bold,
                //       decoration: TextDecoration.underline,
                //       color: incidentLatLng == null ? Colors.grey : Colors.blue,
                //     ),
                //   ),
                // ),
                Text(
                  "Landmark: ${assignedIncident.landmark ?? 'Landmark not available'}",
                  style: TextStyle(
                    fontSize: 18,
                    fontFamily: GoogleFonts.poppins().fontFamily,
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  "Time Reported: ${assignedIncident.timeReported ?? 'Time not available'}",
                  style: TextStyle(
                    fontSize: 18,
                    fontFamily: GoogleFonts.poppins().fontFamily,
                  ),
                ),
                SizedBox(height: 40),
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Row(
                    children: [
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () {},
                          child: Text(
                            'Submit Initial Report',
                            style: TextStyle(
                              fontFamily: GoogleFonts.poppins().fontFamily,
                              fontSize: 11,
                              color: Colors.black,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () {},
                          child: Text(
                            'Submit Progress Report',
                            style: TextStyle(
                              fontFamily: GoogleFonts.poppins().fontFamily,
                              fontSize: 9,
                              color: Colors.black,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 35),
                Center(
                  child: SizedBox(
                    width: 270,
                    height: 55,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8.0),
                        ),
                      ),
                      onPressed: () {},
                      child: Text(
                        'Request for Additional Support',
                        style: TextStyle(
                          fontFamily: GoogleFonts.poppins().fontFamily,
                          fontSize: 16,
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),
                  ),
                ),
                SizedBox(height: 50),
                Divider(color: Colors.black, thickness: 1.5),
                SizedBox(height: 50),
                Center(
                  child: SizedBox(
                    width: 220,
                    height: 55,
                    child: ElevatedButton(
                      style: ButtonStyle(
                        backgroundColor: MaterialStateProperty.all<Color>(
                          Colors.green,
                        ),
                        shape:
                            MaterialStateProperty.all<RoundedRectangleBorder>(
                              RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8.0),
                              ),
                            ),
                      ),
                      onPressed: () async {
                        bool confirm = await showDialog(
                          context: context,
                          builder:
                              (context) => AlertDialog(
                                title: Text('Confirm Action'),
                                content: Text(
                                  'Are you sure you want to mark this incident as "Fire Out"?',
                                ),
                                actions: [
                                  TextButton(
                                    onPressed:
                                        () => Navigator.of(context).pop(false),
                                    child: Text('Cancel'),
                                  ),
                                  TextButton(
                                    onPressed:
                                        () => Navigator.of(context).pop(true),
                                    child: Text(
                                      'Confirm',
                                      style: TextStyle(color: Colors.red),
                                    ),
                                  ),
                                ],
                              ),
                        );

                        if (confirm == true) {
                          await Provider.of<AssignedIncidentProvider>(
                            context,
                            listen: false,
                          ).markIncidentAsContained(context);

                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text('Incident marked as Fire Out!'),
                            ),
                          );

                          Provider.of<FirefighterProvider>(
                            context,
                            listen: false,
                          ).clearAssignedIncident();

                          Navigator.pushAndRemoveUntil(
                            context,
                            MaterialPageRoute(
                              builder: (context) => FirefightersHome(),
                            ),
                            (Route<dynamic> route) => false,
                          );
                        }
                      },

                      child: Text(
                        'Fire Out',
                        style: TextStyle(
                          fontFamily: GoogleFonts.poppins().fontFamily,
                          fontSize: 16,
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                        ),
                      ), // for fire out declaration and submission of final report
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
