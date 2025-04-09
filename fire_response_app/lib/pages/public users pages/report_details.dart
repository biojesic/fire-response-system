import 'package:fire_response_app/pages/public%20users%20pages/edit_report_details.dart';
import 'package:fire_response_app/provider/fire_report_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

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
            Text('Date: ${report.createdAt}', style: TextStyle(fontSize: 18)),
            SizedBox(height: 8),
            Text('Status: ${report.status}', style: TextStyle(fontSize: 18)),
            SizedBox(height: 20),

            if (report.status == 'Ongoing')
              ElevatedButton(
                style: ElevatedButton.styleFrom(minimumSize: Size(120, 40)),
                onPressed: () {
                  // Example: Edit functionality (this can be expanded)
                  // FireReport updatedReport = FireReport(
                  //   id: report.id,
                  //   location: "Updated Location",
                  //   date: report.date,
                  //   status: report.status,
                  // );
                  // fireReportProvider.updateFireReport(
                  //   fireReportProvider.fireReports.indexOf(report),
                  //   updatedReport,
                  // );
                  // Navigator.pop(
                  //   context,
                  // ); Go backto previous page
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
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                minimumSize: Size(100, 40),
              ),
              onPressed: () {
                fireReportProvider.deleteFireReport(report.id);
                Navigator.pop(
                  context,
                ); // Go back to the previous page after updating
              },
              child: Text(
                "Delete Report",
                style: TextStyle(color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
