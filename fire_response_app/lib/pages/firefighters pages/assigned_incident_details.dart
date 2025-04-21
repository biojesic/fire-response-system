import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/provider/assigned_incident_provider.dart';

class AssignedIncidentDetails extends StatefulWidget {
  final int firefighterId; // Pass firefighterId as an argument

  const AssignedIncidentDetails({super.key, required this.firefighterId});

  @override
  State<AssignedIncidentDetails> createState() =>
      _AssignedIncidentDetailsState();
}

class _AssignedIncidentDetailsState extends State<AssignedIncidentDetails> {
  late int firefighterId;

  @override
  void initState() {
    super.initState();
    firefighterId =
        widget.firefighterId; // Get firefighterId passed from Home Page

    // Fetch the assigned incident using the firefighterId
    Provider.of<AssignedIncidentProvider>(
      context,
      listen: false,
    ).fetchAssignedIncident(context); // Pass the firefighterId here
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<AssignedIncidentProvider>(
      builder: (context, assignedIncidentProvider, child) {
        // Access the assigned incident
        final assignedIncident = assignedIncidentProvider.assignedIncident;

        if (assignedIncident == null) {
          // Show loading spinner if no incident is available
          return const Center(child: CircularProgressIndicator());
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
