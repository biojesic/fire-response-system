import 'dart:io';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:fire_response_app/provider/fire_aid_provider.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

class BrgyFireReportDetails extends StatefulWidget {
  final int fireReportId;

  BrgyFireReportDetails({required this.fireReportId});

  @override
  _BrgyFireReportDetailState createState() => _BrgyFireReportDetailState();
}

class _BrgyFireReportDetailState extends State<BrgyFireReportDetails> {
  @override
  void initState() {
    super.initState();
    // Fetch the fire report details when the page loads
    final fireAidProvider = Provider.of<FireAidProvider>(
      context,
      listen: false,
    );
    fireAidProvider.fetchFireReportDetails(widget.fireReportId);
  }

  // Function to format the time from DateTime
  String formatTime(DateTime? createdAt) {
    if (createdAt == null) {
      return ''; // Return an empty string if createdAt is null
    }

    // Format the time as 'HH:mm' (24-hour format)
    return DateFormat('HH:mm').format(createdAt);
  }

  void _showFalseAlarmDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Mark as False Alarm'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text('Are you sure you want to mark this as a false alarm?'),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop(); // Close the dialog
              },
              child: Text('Cancel'),
            ),
            TextButton(
              onPressed: () async {
                // Get the token from AuthProvider
                final authProvider = Provider.of<AuthProvider>(
                  context,
                  listen: false,
                );
                String token =
                    authProvider.token ??
                    ''; // Retrieve token from AuthProvider

                if (token.isNotEmpty) {
                  // Call your provider to handle the false alarm action with the token and fireReportId
                  final fireAidProvider = Provider.of<FireAidProvider>(
                    context,
                    listen: false,
                  );
                  fireAidProvider.markAsFalseAlarm(
                    token,
                    widget.fireReportId,
                  ); // Call with token and fireReportId

                  Navigator.of(context).pop(); // Close the dialog after action
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Report marked as False Alarm!')),
                  );
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Authentication token is missing.')),
                  );
                }
              },
              child: Text('Confirm'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'Fire Report Details',
          style: TextStyle(
            fontFamily: GoogleFonts.poppins().fontFamily,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: Consumer<FireAidProvider>(
        builder: (context, fireAidProvider, child) {
          final fireReport = fireAidProvider.fireReportDetails;

          if (fireReport == null) {
            return Center(
              child: CircularProgressIndicator(),
            ); // Show loading while waiting for data
          }

          return Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        'Location: ${fireReport.location}',
                        style: TextStyle(
                          fontSize: 18,
                          fontFamily: GoogleFonts.poppins().fontFamily,
                        ),
                      ),
                    ),
                    IconButton(
                      onPressed: () {},
                      icon: Icon(Icons.map_outlined),
                    ),
                  ],
                ),
                Text(
                  'Status: ${fireReport.status}',
                  style: TextStyle(
                    fontSize: 18,
                    fontFamily: GoogleFonts.poppins().fontFamily,
                  ),
                ),
                Text(
                  'Description: ${fireReport.description ?? "N/A"}',
                  style: TextStyle(
                    fontSize: 18,
                    fontFamily: GoogleFonts.poppins().fontFamily,
                  ),
                ),
                Text(
                  'Time Reported: ${formatTime(fireReport.createdAt)}',
                  style: TextStyle(
                    fontFamily: GoogleFonts.poppins().fontFamily,
                    fontSize: 16,
                  ),
                ),
                SizedBox(height: 40),
                ElevatedButton(
                  onPressed: () => _showFalseAlarmDialog(context),
                  child: Text('Mark as False Alarm'),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
