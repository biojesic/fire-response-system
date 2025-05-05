import 'package:fire_response_app/pages/components/assigned_incident.dart';
import 'package:fire_response_app/provider/firefighter_provider.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/pages/components/bottom_nav_ff.dart';
import 'package:fire_response_app/pages/firefighters pages/assigned_incident_details.dart';
import 'package:fire_response_app/pages/firefighters pages/firefighters_fire_reports.dart';
import 'package:fire_response_app/pages/firefighters pages/firefighters_settings.dart';

class FirefightersHome extends StatefulWidget {
  const FirefightersHome({super.key});

  @override
  State<FirefightersHome> createState() => _FirefightersHomeState();
}

class _FirefightersHomeState extends State<FirefightersHome> {
  bool _isDataFetched = false;
  String _status = 'Inactive'; // Default status value
  String get status => _status;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initializeData();
    });
  }

  Future<void> _initializeData() async {
    if (_isDataFetched) return;

    final firefighterProvider = Provider.of<FirefighterProvider>(
      context,
      listen: false,
    );

    try {
      int? firefighterId = await firefighterProvider.getSavedFirefighterId();

      if (firefighterId == null) {
        print("❌ Firefighter ID not found in SharedPreferences.");
        int userId = firefighterProvider.userId ?? 0;
        await firefighterProvider.fetchFirefighterDetails(userId, context);
        firefighterId = firefighterProvider.getFirefighterId;

        if (firefighterId != null) {
          await firefighterProvider.saveFirefighterId(firefighterId);
          print("✅ Firefighter ID saved: $firefighterId");
        } else {
          print("❌ Still no Firefighter ID after fetch.");
          return;
        }
      } else {
        print("✅ Firefighter ID from SharedPreferences: $firefighterId");
        firefighterProvider.setFirefighterId(firefighterId);
      }

      await firefighterProvider.fetchAssignedIncident(firefighterId);

      // Check if the widget is still mounted before calling setState()
      if (mounted) {
        setState(() {
          _isDataFetched = true;
        });
      }
    } catch (e) {
      print("🔥 Initialization error: $e");
    }
  }

  Widget _buildIncidentWidget(FirefighterProvider firefighterProvider) {
    final assignedIncident = firefighterProvider.assignedIncident;

    if (assignedIncident == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0, vertical: 20),
          child: Card(
            elevation: 3,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(15),
            ),
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    Icons.local_fire_department_outlined,
                    size: 48,
                    color: Colors.grey,
                  ),
                  SizedBox(height: 12),
                  Text(
                    "No Assigned Incident",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  SizedBox(height: 8),
                  Text(
                    "You're currently on standby.\nPlease be alert.",
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                  ),
                ],
              ),
            ),
          ),
        ),
      );
    } else {
      return InkWell(
        onTap: () {
          // Navigate to AssignedIncidentDetails page
          Navigator.push(
            context,
            MaterialPageRoute(
              builder:
                  (context) =>
                      AssignedIncidentDetails(), // No parameters passed here
            ),
          );
        },
        child: Material(
          child: Center(
            child: SizedBox(
              width: 350,
              height: 180,
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
                        value: assignedIncident.location ?? 'Loading...',
                      ),
                      InfoRow(
                        icon: Icons.map,
                        label: "Landmark:",
                        value: assignedIncident.landmark ?? 'Loading...',
                      ),
                      InfoRow(
                        icon: Icons.access_time,
                        label: "Time Reported:",
                        value: assignedIncident.timeReported ?? 'Loading...',
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<FirefighterProvider>(
      builder: (context, firefighterProvider, child) {
        return Scaffold(
          appBar: AppBar(
            title: Center(
              child: Text(
                'Dashboard',
                style: TextStyle(
                  fontFamily: GoogleFonts.poppins().fontFamily,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
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
                        'Welcome, ${firefighterProvider.firefighterName}!',
                        style: TextStyle(
                          fontFamily: GoogleFonts.poppins().fontFamily,
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Column(
                        children: [
                          ClipOval(
                            child: Image.asset(
                              'lib/images/pfp.jpg',
                              height: 40,
                              width: 40,
                            ),
                          ),
                          Text(
                            firefighterProvider.firefighterRole,
                            style: TextStyle(
                              fontFamily: GoogleFonts.poppins().fontFamily,
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
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    const SizedBox(width: 10),
                    Switch(value: false, onChanged: (bool newValue) {}),
                    SizedBox(width: 3),
                    Text(
                      "${firefighterProvider.status}",
                      style: TextStyle(
                        fontFamily: GoogleFonts.poppins().fontFamily,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.black,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 50),
                _buildIncidentWidget(firefighterProvider),
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
}
