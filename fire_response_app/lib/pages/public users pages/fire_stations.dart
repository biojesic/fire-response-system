import 'package:fire_response_app/pages/components/bottom_nav.dart';
import 'package:fire_response_app/pages/public%20users%20pages/civilians_home.dart';
import 'package:fire_response_app/pages/public%20users%20pages/fire_reports.dart';
import 'package:fire_response_app/pages/public%20users%20pages/profile.dart';
import 'package:fire_response_app/pages/public%20users%20pages/station_details.dart';
import 'package:fire_response_app/provider/fire_stations_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class FireStations extends StatefulWidget {
  const FireStations({super.key});

  @override
  State<FireStations> createState() => _FireStationsState();
}

class _FireStationsState extends State<FireStations> {
  @override
  void initState() {
    super.initState();
    Provider.of<FireStationProvider>(
      context,
      listen: false,
    ).fetchFireStations();
  }

  final TextEditingController searchController = TextEditingController();
  final int selectedIndex = 2;

  void _onItemTapped(int index) {
    if (index == selectedIndex) return;

    Widget nextPage;
    switch (index) {
      case 0:
        nextPage = CiviliansHomePage();
        break;
      case 1:
        nextPage = FireReports();
        break;
      case 2:
        nextPage = FireStations();
        break;
      case 3:
        nextPage = Profile();
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
    final fireStationProvider = context.watch<FireStationProvider>();
    return Scaffold(
      appBar: AppBar(title: Text('Fire Stations')),
      body:
          fireStationProvider.fireStation.isEmpty
              ? Center(child: Text("No fire stations available."))
              : Column(
                children: [
                  Padding(
                    padding: EdgeInsets.fromLTRB(10, 5, 20, 10),
                    child: Row(
                      children: [
                        Expanded(
                          child: Padding(
                            padding: EdgeInsets.fromLTRB(30, 0, 0, 10),
                            child: Container(
                              height: 45,
                              decoration: BoxDecoration(
                                color:
                                    Colors
                                        .white, // (Optional) Lagyan ng background para kita ang border
                                borderRadius: BorderRadius.circular(50),
                                border: Border.all(
                                  color: Colors.black,
                                  width: 1,
                                ), // Outline border
                              ),
                              child: TextFormField(
                                controller: searchController,
                                decoration: InputDecoration(
                                  hintText:
                                      'Search the Nearest Fire Station...',
                                  border: InputBorder.none,
                                  contentPadding: EdgeInsets.symmetric(
                                    horizontal: 13,
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
                          onPressed: () {},
                        ),
                      ],
                    ),
                  ),
                  SizedBox(height: 5),
                  Expanded(
                    child: ListView.builder(
                      itemCount: fireStationProvider.fireStation.length,
                      itemBuilder: (context, index) {
                        final station = fireStationProvider.fireStation[index];
                        return Material(
                          color: Colors.transparent,
                          child: InkWell(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder:
                                      (context) =>
                                          StationDetails(station: station),
                                ),
                              );
                            },
                            child: Card(
                              margin: EdgeInsets.symmetric(
                                horizontal: 16,
                                vertical: 6,
                              ),
                              child: Padding(
                                padding: EdgeInsets.all(13.0),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: <Widget>[
                                    Text(
                                      station.firestationName,
                                      style: TextStyle(
                                        fontSize: 20,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    SizedBox(height: 5),
                                    Text(
                                      'Location: ${station.firestationLocation}',
                                    ),
                                    Text(
                                      'Contact: ${station.firestationContactNumber}',
                                    ),
                                    SizedBox(height: 5),
                                    // if (station.status == 'Active')
                                    //   Text(
                                    //     station.status,
                                    //     style: TextStyle(
                                    //       color: Colors.green[600],
                                    //       fontSize: 15,
                                    //       fontWeight: FontWeight.w600,
                                    //     ),
                                    //   )
                                    // else
                                    //   Text(
                                    //     station.status,
                                    //     style: TextStyle(
                                    //       color: Colors.red,
                                    //       fontSize: 15,
                                    //       fontWeight: FontWeight.w600,
                                    //     ),
                                    //   ),
                                    SizedBox(height: 7),
                                    Row(
                                      children: [
                                        TextButton(
                                          style: TextButton.styleFrom(
                                            backgroundColor: Colors.green[100],
                                          ),
                                          onPressed: () {},
                                          child: Row(
                                            children: [
                                              Icon(
                                                Icons.call,
                                                color: Colors.black,
                                              ),
                                              SizedBox(width: 4),
                                              Text(
                                                'Call Now',
                                                style: TextStyle(
                                                  color: Colors.black,
                                                  fontWeight: FontWeight.w600,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        SizedBox(width: 8),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
      bottomNavigationBar: BottomNav(
        currentIndex: selectedIndex,
        onTap: _onItemTapped,
      ),
    );
  }
}
