import 'package:fire_response_app/pages/components/bottom_nav_ff.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_home.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_settings.dart';
import 'package:fire_response_app/provider/firefighter_reports_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class FirefightersFireReports extends StatefulWidget {
  const FirefightersFireReports({super.key});

  @override
  State<FirefightersFireReports> createState() =>
      _FirefightersFireReportsState();
}

class _FirefightersFireReportsState extends State<FirefightersFireReports> {
  final TextEditingController searchController = TextEditingController();
  final int selectedIndex = 1;

  @override
  void initState() {
    super.initState();
    // Fetching fire reports when the page loads
    Provider.of<FirefighterReportsProvider>(
      context,
      listen: false,
    ).fetchFireReports();
  }

  void onItemTapped(int index) {
    if (index == selectedIndex) return;

    Widget nextPage;
    switch (index) {
      case 0:
        nextPage = FirefightersHome();
        break;
      case 1:
        nextPage = FirefightersFireReports();
        break;
      case 2:
        nextPage = FirefightersSettings();
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
    final fireReportProvider = Provider.of<FirefighterReportsProvider>(context);

    return Scaffold(
      appBar: AppBar(title: Text('Firefighter Reports')),
      body: Column(
        children: [
          Padding(
            padding: EdgeInsets.fromLTRB(10, 0, 25, 5),
            child: Row(
              children: [
                Expanded(
                  child: Padding(
                    padding: EdgeInsets.fromLTRB(30, 0, 0, 0),
                    child: Container(
                      height: 45,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(50),
                        border: Border.all(color: Colors.black, width: 1),
                      ),
                      child: TextFormField(
                        controller: searchController,
                        decoration: InputDecoration(
                          hintText: 'Search report ...',
                          border: InputBorder.none,
                          contentPadding: EdgeInsets.symmetric(
                            horizontal: 20,
                            vertical: 10,
                          ),
                        ),
                        style: TextStyle(fontSize: 16),
                      ),
                    ),
                  ),
                ),
                IconButton(
                  icon: Icon(Icons.search, size: 40),
                  onPressed: () {
                    // Implement your search functionality here
                  },
                ),
              ],
            ),
          ),
          Container(
            padding: EdgeInsets.all(10),
            color: Colors.grey[300],
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    "Type",
                    style: TextStyle(fontWeight: FontWeight.bold),
                    textAlign: TextAlign.center,
                  ),
                ),
                Expanded(
                  child: Text(
                    "Date",
                    style: TextStyle(fontWeight: FontWeight.bold),
                    textAlign: TextAlign.center,
                  ),
                ),
                Expanded(
                  child: Text(
                    "Actions",
                    style: TextStyle(fontWeight: FontWeight.bold),
                    textAlign: TextAlign.center,
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child:
                fireReportProvider.fireReports.isEmpty
                    ? Center(
                      child:
                          fireReportProvider.isLoading
                              ? CircularProgressIndicator() // Show loading indicator if fetching
                              : Text(
                                "No data available",
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ), // Show no data message
                    )
                    : ListView.builder(
                      itemCount: fireReportProvider.fireReports.length,
                      itemBuilder: (context, index) {
                        final report = fireReportProvider.fireReports[index];

                        return Card(
                          margin: EdgeInsets.symmetric(
                            vertical: 5,
                            horizontal: 10,
                          ),
                          child: Padding(
                            padding: EdgeInsets.all(10),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Expanded(
                                  child: Text(
                                    report
                                        .reportType, // Assuming location is part of the report
                                    textAlign: TextAlign.center,
                                  ),
                                ),
                                Expanded(
                                  child: Text(
                                    report
                                        .createdAt, // Assuming date is part of the report
                                    textAlign: TextAlign.center,
                                  ),
                                ),
                                Expanded(
                                  child: Center(
                                    child: TextButton(
                                      onPressed: () {
                                        // Add navigation to detailed report page here
                                      },
                                      child: Text(
                                        "View Details",
                                        textAlign: TextAlign.center,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
          ),
        ],
      ),
      bottomNavigationBar: BottomNavFF(
        currentIndex: selectedIndex,
        onTap: onItemTapped,
      ),
    );
  }
}
