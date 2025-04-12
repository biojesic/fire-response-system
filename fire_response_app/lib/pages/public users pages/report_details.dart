import 'package:fire_response_app/pages/public%20users%20pages/edit_report_details.dart';
import 'package:fire_response_app/provider/fire_report_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:get_time_ago/get_time_ago.dart' as timeago;

class ReportDetails extends StatefulWidget {
  const ReportDetails({super.key});

  @override
  State<ReportDetails> createState() => _ReportDetailsState();
}

class _ReportDetailsState extends State<ReportDetails> {
  @override
  Widget build(BuildContext context) {
    final fireReportProvider = context.watch<FireReportProvider>();
    final report = fireReportProvider.selectedReport;

    if (report == null) {
      return Scaffold(
        appBar: AppBar(title: Text("Report Details")),
        body: Center(child: Text("No report selected.")),
      );
    }

    String relativeTime = timeago.GetTimeAgo.parse(report.createdAt);

    return Scaffold(
      appBar: AppBar(title: Text('Report Details')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Location: ${report.location}',
              style: TextStyle(fontSize: 18),
            ),
            SizedBox(height: 8),
            Text('Reported: $relativeTime', style: TextStyle(fontSize: 18)),
            SizedBox(height: 8),
            Text('Status: ${report.status}', style: TextStyle(fontSize: 18)),
            SizedBox(height: 20),

            if (report.status == 'Pending')
              ElevatedButton(
                style: ElevatedButton.styleFrom(minimumSize: Size(120, 40)),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => EditDetails(report: report),
                    ),
                  );
                },
                child: Text(
                  "Edit Report",
                  style: TextStyle(color: Colors.black),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
