import 'package:fire_response_app/pages/components/assigned_incident.dart';
import 'package:fire_response_app/provider/firefighter_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/pages/components/bottom_nav_ff.dart';
import 'package:fire_response_app/pages/firefighters%20pages/assigned_incident_details.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_fire_reports.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_settings.dart';

class FirefightersHome extends StatefulWidget {
  const FirefightersHome({super.key});

  @override
  State<FirefightersHome> createState() => _FirefightersHomeState();
}

class _FirefightersHomeState extends State<FirefightersHome> {
  int firefighterId = 0; // Placeholder ID

  @override
  void initState() {
    super.initState();
    // Fetch data when the screen is initialized
    Provider.of<FirefighterProvider>(
      context,
      listen: false,
    ).fetchFirefighterStatus(firefighterId);
    Provider.of<FirefighterProvider>(
      context,
      listen: false,
    ).fetchFirefighterDetails(firefighterId, context);
  }

  // Handle bottom navigation tap
  void onItemTapped(int index) {
    Widget nextPage;
    switch (index) {
      case 0:
        nextPage = const FirefightersHome();
        break;
      case 1:
        nextPage = const FirefightersFireReports();
        break;
      case 2:
        nextPage = const FirefightersSettings();
        break;
      default:
        return;
    }

    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (context) => nextPage),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<FirefighterProvider>(
      builder: (context, provider, child) {
        return Scaffold(
          appBar: AppBar(title: const Text('Dashboard')),
          body: Padding(
            padding: const EdgeInsets.fromLTRB(12, 5, 12, 5),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(18, 5, 24, 5),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Welcome, ${provider.firefighterName}!',
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Column(
                        children: [
                          ClipOval(
                            child: Image.asset(
                              'lib/images/pfp.jpg', // Placeholder profile image
                              height: 40,
                              width: 40,
                            ),
                          ),
                          Text(
                            provider
                                .firefighterRole, // Display firefighter role
                            style: const TextStyle(
                              fontWeight: FontWeight.w500,
                              fontSize: 15,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 10),
                Center(
                  child: Text(
                    "Status: ${provider.status}", // Displays firefighter status
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black,
                    ),
                  ),
                ),
                const SizedBox(height: 50),
                GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const AssignedIncidentDetails(),
                      ),
                    );
                  },
                  child: SizedBox(
                    width: double.infinity,
                    height: 230,
                    child: Card(
                      elevation: 4,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(15),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "Assigned Incident",
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: Colors.red[800],
                              ),
                            ),
                            const Divider(),
                            InfoRow(
                              icon: Icons.location_on,
                              label: "Location:",
                              value: "Brgy. Langkaan 1",
                            ),
                            InfoRow(
                              icon: Icons.map,
                              label: "Landmark:",
                              value: "Near Gas Station",
                            ),
                            InfoRow(
                              icon: Icons.access_time,
                              label: "Time Reported:",
                              value: "10:30 AM",
                            ),
                            InfoRow(
                              icon: Icons.fire_truck,
                              label: "Responding Team:",
                              value: "Team Alpha",
                            ),
                            InfoRow(
                              icon: Icons.warning,
                              label: "Incident Type:",
                              value: "Structural Fire",
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          bottomNavigationBar: BottomNavFF(
            currentIndex: 0,
            onTap: onItemTapped,
          ),
        );
      },
    );
  }
}
