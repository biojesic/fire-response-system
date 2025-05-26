import 'package:fire_response_app/pages/brgy_fire_aid_pages/fire_aid_home.dart';
import 'package:fire_response_app/pages/brgy_fire_aid_pages/fire_aid_settings.dart';
import 'package:fire_response_app/pages/components/bottom_nav_fireaid.dart';
import 'package:flutter/material.dart';

class FireAidReports extends StatefulWidget {
  const FireAidReports({super.key});

  @override
  State<FireAidReports> createState() => _FireAidReportsState();
}

class _FireAidReportsState extends State<FireAidReports> {
  final int selectedIndex = 1;
  final TextEditingController searchController = TextEditingController();

  void onItemTapped(int index) {
    if (index == selectedIndex) return;

    Widget nextPage;
    switch (index) {
      case 0:
        nextPage = FireAidHomePage();
        break;
      case 1:
        nextPage = FireAidReports();
        break;
      case 2:
        nextPage = FireAidSettings();
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
                    "Location",
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
            child: Card(
              margin: EdgeInsets.symmetric(vertical: 5, horizontal: 10),
              child: Padding(
                padding: EdgeInsets.all(10),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text('Location 1', textAlign: TextAlign.center),
                    ),
                    Expanded(
                      child: Text(
                        '07-10-2000', // Format the date as needed
                        textAlign: TextAlign.center,
                      ),
                    ),
                    Expanded(
                      child: Center(
                        child: TextButton(
                          onPressed: () {
                            // final fireReportId =
                            //     report
                            //         .reportId; // Get the report ID from the current report

                            // // Navigate to the details page and pass the fireReportId
                            // Navigator.push(
                            //   context,
                            //   MaterialPageRoute(
                            //     builder:
                            //         (context) =>
                            //             FirefighterReportsViewDetails(
                            //               fireReportId:
                            //                   fireReportId!,
                            //             ),
                            //   ),
                            // );
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
            ),
          ),
        ],
      ),
      bottomNavigationBar: BottomNavFireaid(
        currentIndex: selectedIndex,
        onTap: onItemTapped,
      ),
    );
  }
}
