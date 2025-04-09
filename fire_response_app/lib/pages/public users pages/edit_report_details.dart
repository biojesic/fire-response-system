import 'package:fire_response_app/provider/fire_report_provider.dart';
import 'package:flutter/material.dart';
import 'package:fire_response_app/models/fire_reports.dart';
import 'package:provider/provider.dart';

class EditDetails extends StatefulWidget {
  final FireReport report; // Receive the report data

  const EditDetails({super.key, required this.report});

  @override
  State<EditDetails> createState() => _EditDetailsState();
}

class _EditDetailsState extends State<EditDetails> {
  final nameController = TextEditingController();
  final descriptionController = TextEditingController();
  final locationController = TextEditingController();
  final landmarkController = TextEditingController();

  @override
  void initState() {
    super.initState();
    // Initialize controllers with the selected report's data
    descriptionController.text = widget.report.description;
    locationController.text = widget.report.location;
    landmarkController.text = widget.report.landmark;
  }

  @override
  void dispose() {
    // Don't forget to dispose controllers when done to avoid memory leaks
    descriptionController.dispose();
    locationController.dispose();
    landmarkController.dispose();
    super.dispose();
  }

  // Function to show the confirmation dialog before saving
  Future<void> _showConfirmationDialog() async {
    return showDialog<void>(
      context: context,
      barrierDismissible: false, // User must confirm or cancel
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Confirm Save'),
          content: Text('Are you sure you want to save these changes?'),
          actions: <Widget>[
            TextButton(
              onPressed: () {
                Navigator.of(context).pop(); // Close the dialog without saving
              },
              child: Text('Cancel'),
            ),
            TextButton(
              onPressed: () {
                // Create the updated report
                FireReport updatedReport = FireReport(
                  id: widget.report.id,
                  reportedBy: widget.report.reportedBy,
                  fireStationId: widget.report.fireStationId,
                  location: locationController.text, // Get updated location
                  landmark: landmarkController.text, // Get updated landmark
                  description:
                      descriptionController.text, // Get updated description
                  createdAt: widget.report.createdAt, // Date remains unchanged
                  status: widget.report.status, // Status remains unchanged
                );

                // Update the report in the provider
                context.read<FireReportProvider>().updateFireReport(
                  context.read<FireReportProvider>().fireReports.indexOf(
                    widget.report,
                  ),
                  updatedReport,
                );

                Navigator.pop(context); // Close the confirmation dialog
                Navigator.pop(context); // Go back after updating the report
              },
              child: Text('Save'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Edit Details")),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            children: [
              Text(
                "Name: ${widget.report.reportedBy}",
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 20),
              Text(
                "Location",
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 3),
              TextFormField(
                controller: locationController,
                decoration: InputDecoration(border: OutlineInputBorder()),
              ),
              SizedBox(height: 20),
              Text(
                "Landmark",
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 3),
              TextFormField(
                controller: landmarkController,
                decoration: InputDecoration(border: OutlineInputBorder()),
              ),
              SizedBox(height: 20),
              Text(
                "Description",
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 3),
              TextFormField(
                controller: descriptionController,
                decoration: InputDecoration(border: OutlineInputBorder()),
              ),
              SizedBox(height: 20),
              Text(
                "Date: ${widget.report.createdAt}",
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 20),
              Text(
                "Status: ${widget.report.status}",
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 70),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color.fromARGB(255, 108, 248, 113),
                ),
                onPressed: () {
                  _showConfirmationDialog();
                },
                child: Text(
                  "Save Changes",
                  style: TextStyle(
                    color: Colors.black,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
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
