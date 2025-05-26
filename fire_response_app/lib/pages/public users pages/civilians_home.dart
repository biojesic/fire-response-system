import 'dart:async';
import 'package:fire_response_app/pages/components/bottom_nav.dart';
import 'package:fire_response_app/pages/public%20users%20pages/fire_reports.dart';
import 'package:fire_response_app/pages/public%20users%20pages/fire_stations.dart';
import 'package:fire_response_app/pages/public%20users%20pages/profile.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
// import 'package:fire_response_app/pages/public%20users%20pages/submit_report.dart';
import 'package:fire_response_app/provider/firefighter_provider.dart';
import 'package:fire_response_app/provider/submit_report_provider.dart';
import 'package:fire_response_app/provider/user_provider.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class CiviliansHomePage extends StatefulWidget {
  const CiviliansHomePage({super.key});

  @override
  State<CiviliansHomePage> createState() => _CiviliansHomePageState();
}

class _CiviliansHomePageState extends State<CiviliansHomePage> {
  int selectedIndex = 0;
  bool isPressing = false;
  Timer? _pressTimer;

  void onItemTapped(int index) {
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

  void _onLongPressStart(LongPressStartDetails details) {
    _pressTimer = Timer(Duration(seconds: 3), () async {
      bool confirm = await showDialog(
        context: context,
        builder:
            (context) => AlertDialog(
              title: Text("Confirm Fire Report"),
              content: Text("Do you want to send a fire emergency report?"),
              actions: [
                TextButton(
                  onPressed: () => Navigator.of(context).pop(false),
                  child: Text("Cancel"),
                ),
                TextButton(
                  onPressed: () => Navigator.of(context).pop(true),
                  child: Text("Confirm"),
                ),
              ],
            ),
      );

      if (confirm) {
        await Provider.of<SubmitReportProvider>(
          context,
          listen: false,
        ).quickReport(context);
      }
    });
  }

  void _onLongPressEnd(LongPressEndDetails details) {
    setState(() {
      isPressing = false;
    });

    // Cancel the timer if the press ended before 3 seconds
    if (_pressTimer != null) {
      _pressTimer!.cancel();
    }
  }

  @override
  void initState() {
    super.initState();
    _saveFcmTokenIfNeeded();

    // Set a delay to show the notification for 10 seconds
    Future.delayed(Duration(seconds: 2), () {
      // Show the notification
      Provider.of<UserProvider>(
        context,
        listen: false,
      ).toggleNotificationVisibility(true);

      // Hide the notification after 10 seconds
      Future.delayed(Duration(seconds: 10), () {
        Provider.of<UserProvider>(
          context,
          listen: false,
        ).toggleNotificationVisibility(false);
      });
    });
  }

  // Define _saveFcmTokenIfNeeded method in the State class
  Future<void> _saveFcmTokenIfNeeded() async {
    // Fetch the FCM token
    String? fcmToken = await FirebaseMessaging.instance.getToken();

    if (fcmToken != null) {
      // Save the FCM token in the AuthProvider
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      await authProvider.saveFcmToken(fcmToken); // Save token to backend
    }
  }

  @override
  Widget build(BuildContext context) {
    final firefighterProvider = Provider.of<FirefighterProvider>(context);

    return Scaffold(
      appBar: AppBar(title: Text('Fire Response App')),
      body: SingleChildScrollView(
        child: Padding(
          padding: EdgeInsets.fromLTRB(0, 60, 0, 10),
          child: Column(
            children: [
              GestureDetector(
                // onTap: () {
                //   Navigator.push(
                //     context,
                //     MaterialPageRoute(builder: (context) => SubmitReportPage()),
                //   );
                // },
                onLongPressStart: _onLongPressStart,
                onLongPressEnd: _onLongPressEnd,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    AnimatedContainer(
                      duration: Duration(seconds: 3),
                      width: 170,
                      height: 170,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: Colors.red,
                      ),
                      child: ShaderMask(
                        shaderCallback:
                            (bounds) => LinearGradient(
                              colors: [Colors.red, Colors.yellow, Colors.green],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ).createShader(bounds),
                        child: Container(
                          decoration: BoxDecoration(shape: BoxShape.circle),
                        ),
                      ),
                    ),
                    SizedBox(
                      width: 130,
                      child: Text(
                        "Report A Fire Emergency",
                        textAlign: TextAlign.center,
                        maxLines: 2,
                        softWrap: true,
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 18,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(height: 100),
              // ETA Section
              // Consumer<FirefighterProvider>(
              //   builder: (context, firefighterProvider, child) {
              //     return Stack(
              //       alignment: Alignment.center,
              //       children: [
              //         Container(
              //           width: 250,
              //           height: 85,
              //           decoration: BoxDecoration(
              //             color: Colors.blue[200],
              //             borderRadius: BorderRadius.circular(30),
              //             boxShadow: [
              //               BoxShadow(
              //                 color: Colors.black26,
              //                 blurRadius: 5,
              //                 offset: Offset(0, 2),
              //               ),
              //             ],
              //           ),
              //         ),
              //         SizedBox(
              //           width: 200,
              //           child: Text(
              //             firefighterProvider.getEtaForCivilian != null
              //                 ? "Fire Responders are on the way. ETA: ${firefighterProvider.getEtaForCivilian}"
              //                 : "Fetching ETA...",
              //             textAlign: TextAlign.center,
              //             maxLines: 3,
              //             softWrap: true,
              //             style: TextStyle(
              //               color: Colors.black,
              //               fontWeight: FontWeight.bold,
              //               fontSize: 17,
              //             ),
              //           ),
              //         ),
              //       ],
              //     );
              //   },
              // ),
              // Sliding Notification (placed here, after the content above)
              Consumer<UserProvider>(
                builder: (context, userProvider, child) {
                  return AnimatedPositioned(
                    duration: Duration(milliseconds: 500),
                    top: userProvider.isNotificationVisible ? 0 : -100,
                    left: 0,
                    right: 0,
                    child: AnimatedOpacity(
                      opacity: userProvider.isNotificationVisible ? 1.0 : 0.0,
                      duration: Duration(milliseconds: 500),
                      child: Container(
                        color: Colors.red,
                        padding: EdgeInsets.all(10),
                        child: Row(
                          children: [
                            Icon(Icons.notifications, color: Colors.white),
                            SizedBox(width: 10),
                            Expanded(
                              child: Text(
                                "Ongoing Fire in Location 1",
                                style: TextStyle(color: Colors.white),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),

      bottomNavigationBar: BottomNav(
        currentIndex: selectedIndex,
        onTap: onItemTapped,
      ),
    );
  }
}
