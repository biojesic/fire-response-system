import 'package:fire_response_app/models/brgy_fire_aid.dart';
import 'package:fire_response_app/pages/brgy_fire_aid_pages/brgy_fire_report_details.dart';
import 'package:fire_response_app/pages/brgy_fire_aid_pages/fire_aid_reports.dart';
import 'package:fire_response_app/pages/brgy_fire_aid_pages/fire_aid_settings.dart';
import 'package:fire_response_app/pages/components/bottom_nav_fireaid.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:fire_response_app/provider/fire_aid_provider.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

class FireAidHomePage extends StatefulWidget {
  const FireAidHomePage({super.key});

  @override
  State<FireAidHomePage> createState() => _FireAidHomePageState();
}

class _FireAidHomePageState extends State<FireAidHomePage> {
  @override
  void initState() {
    super.initState();
    final fireAidProvider = Provider.of<FireAidProvider>(
      context,
      listen: false,
    );
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final userToken = authProvider.token;

    if (userToken != null) {
      fireAidProvider.fetchFireAidData(userToken); // Fetch fire aid data
      fireAidProvider.fetchFireIncidents(
        userToken,
        1,
      ); // Fetch fire incidents for Barangay 1
    } else {
      print("No token found. User not authenticated.");
    }
  }

  // Function to format the time from DateTime
  String formatTime(DateTime? createdAt) {
    if (createdAt == null) {
      return ''; // Return an empty string if createdAt is null
    }

    // Format the time as 'HH:mm' (24-hour format)
    return DateFormat('HH:mm').format(createdAt);
  }

  void onItemTapped(int index) {
    Widget nextPage;
    switch (index) {
      case 0:
        nextPage = const FireAidHomePage();
        break;
      case 1:
        nextPage = const FireAidReports();
        break;
      case 2:
        nextPage = const FireAidSettings();
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
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(12, 5, 12, 5),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(18, 5, 24, 5),
                child: Consumer<FireAidProvider>(
                  builder: (context, fireAidProvider, child) {
                    FireAid? fireAid = fireAidProvider.fireAid;

                    // Show a loading spinner if fireAid data isn't available yet
                    if (fireAid == null) {
                      return Center(child: CircularProgressIndicator());
                    }

                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (fireAid.user != null)
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'Welcome, ${fireAid.user?.firstName}!',
                                style: TextStyle(
                                  fontFamily: GoogleFonts.poppins().fontFamily,
                                  fontSize: 24,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              Text(
                                'Fire Aid',
                                style: TextStyle(
                                  fontFamily: GoogleFonts.poppins().fontFamily,
                                  fontSize: 17,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        SizedBox(height: 3),
                        if (fireAid.barangay != null)
                          Text(
                            'Barangay ${fireAid.barangay?.barangay_name}',
                            style: TextStyle(
                              fontFamily: GoogleFonts.poppins().fontFamily,
                              fontSize: 16,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                      ],
                    );
                  },
                ),
              ),
              const SizedBox(height: 10),
              Padding(
                padding: const EdgeInsets.all(18.0),
                child: Consumer<FireAidProvider>(
                  builder: (context, fireAidProvider, child) {
                    var fireReports = fireAidProvider.fireReports;

                    // Show a loading spinner if fireReports data isn't available yet
                    if (fireReports.isEmpty) {
                      return Center(child: CircularProgressIndicator());
                    }

                    // Display all the fire reports in a ListView
                    return ListView.builder(
                      shrinkWrap:
                          true, // Use shrinkWrap to make it scrollable inside Column
                      physics:
                          NeverScrollableScrollPhysics(), // Disable the ListView scroll
                      itemCount: fireReports.length,
                      itemBuilder: (context, index) {
                        var report = fireReports[index];
                        if (report.status == 'Pending') {
                          return Padding(
                            padding: const EdgeInsets.fromLTRB(38, 10, 38, 10),
                            child: InkWell(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder:
                                        (context) => BrgyFireReportDetails(
                                          fireReportId: report.id,
                                        ), // No parameters passed here
                                  ),
                                );
                              },
                              child: Card(
                                elevation: 4,
                                margin: const EdgeInsets.symmetric(
                                  vertical: 10,
                                ),
                                child: Padding(
                                  padding: const EdgeInsets.all(12.0),
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Location: ${report.location}',
                                        style: TextStyle(
                                          fontFamily:
                                              GoogleFonts.poppins().fontFamily,
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 5),
                                      Text(
                                        'Landmark: ${report.landmark}',
                                        style: TextStyle(
                                          fontFamily:
                                              GoogleFonts.poppins().fontFamily,
                                          fontSize: 16,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                      const SizedBox(height: 5),
                                      Text(
                                        'Time Reported: ${formatTime(report.createdAt)}',
                                        style: TextStyle(
                                          fontFamily:
                                              GoogleFonts.poppins().fontFamily,
                                          fontSize: 16,
                                        ),
                                      ),
                                      const SizedBox(height: 5),
                                      Text(
                                        'Contact Info: ${report.contactInfo ?? 'N/A'}',
                                        style: TextStyle(
                                          fontFamily:
                                              GoogleFonts.poppins().fontFamily,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          );
                        } else {
                          return Container(); // If not Pending, don't show the report
                        }
                      },
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: BottomNavFireaid(
        currentIndex: 0,
        onTap: onItemTapped,
      ),
    );
  }
}
