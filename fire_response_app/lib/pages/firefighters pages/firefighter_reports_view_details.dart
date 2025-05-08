import 'package:fire_response_app/pages/firefighters%20pages/final_real_time_report.dart';
import 'package:fire_response_app/provider/firefighter_reports_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/models/firefighter_report.dart'; // Import the FireFighterReport model

class FirefighterReportsViewDetails extends StatefulWidget {
  final int fireReportId;

  FirefighterReportsViewDetails({required this.fireReportId});

  @override
  _FirefighterReportsViewDetailsState createState() =>
      _FirefighterReportsViewDetailsState();
}

class _FirefighterReportsViewDetailsState
    extends State<FirefighterReportsViewDetails> {
  @override
  void initState() {
    super.initState();
    // Fetch the fire report details when the screen is initialized
    Provider.of<FirefighterReportsProvider>(
      context,
      listen: false,
    ).fetchFireReportDetails(widget.fireReportId);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Fire Report Details'),
        backgroundColor: Colors.blue,
        elevation: 4,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: Consumer<FirefighterReportsProvider>(
        builder: (context, provider, child) {
          // Check if data is loading
          if (provider.isLoading) {
            return Center(child: CircularProgressIndicator());
          }

          // Get the selected report details
          final fireReportDetails = provider.selectReport;

          if (fireReportDetails == null) {
            return Center(child: Text('No report details found.'));
          }

          return Padding(
            padding: const EdgeInsets.all(16.0),
            child: ListView(
              children: [
                Text(
                  'Fire Report #${widget.fireReportId}',
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                ),
                SizedBox(height: 16),
                // buildDetailCard("Location", fireReportDetails.location),
                // buildDetailCard("Landmark", fireReportDetails.landmark),
                // buildDetailCard("Description", fireReportDetails.description),
                // buildDetailCard("Contact Info", fireReportDetails.contactInfo),
                // buildDetailCard("Status", fireReportDetails.status),
                // buildDetailCard(
                //   "Marked As Contained By",
                //   fireReportDetails.markedAsContainedById,
                // ),
                // buildDetailCard(
                //   "Marked As Contained At",
                //   fireReportDetails.markedAsContainedAt,
                // ),
                SizedBox(height: 40),
                SizedBox(
                  height: 45,
                  width: 5,
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder:
                              (context) => FinalRealTimeReport(
                                fireReportId: widget.fireReportId,
                              ),
                        ),
                      );
                    },
                    child: Text('Submit Final Real Time Report'),
                  ),
                ),
                SizedBox(height: 10),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget buildDetailCard(String title, String value) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8.0),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(title, style: TextStyle(fontWeight: FontWeight.bold)),
            Expanded(
              child: Text(
                value,
                textAlign: TextAlign.end,
                style: TextStyle(color: Colors.black54),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
