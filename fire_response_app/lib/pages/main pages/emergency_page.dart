import 'package:fire_response_app/pages/auth%20pages/login.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class EmergencyPage extends StatefulWidget {
  const EmergencyPage({super.key});

  @override
  State<EmergencyPage> createState() => _EmergencyPageState();
}

class _EmergencyPageState extends State<EmergencyPage> {
  @override
  Widget build(BuildContext context) {
    // Check if the user is logged in by consuming the AuthProvider
    final isLoggedIn = Provider.of<AuthProvider>(context).isLoggedIn;

    return Consumer<AuthProvider>(
      builder:
          (context, value, child) => Scaffold(
            appBar: AppBar(
              title: Text('Emergency'),
              backgroundColor: Colors.white,
              elevation: 0,
            ),
            body: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    const SizedBox(height: 30),
                    Text(
                      'Hold the button to alert the nearest fire station',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    SizedBox(height: 20),
                    GestureDetector(
                      onTap: () {
                        // Handle the tap event (e.g., trigger emergency alert)
                      },
                      child: Container(
                        width: 150,
                        height: 150,
                        decoration: BoxDecoration(
                          color: Colors.red,
                          shape: BoxShape.circle,
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black26,
                              blurRadius: 5,
                              offset: Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Icon(Icons.sos, color: Colors.white, size: 80),
                      ),
                    ),
                    SizedBox(height: 40),
                    // Container(
                    //   padding: EdgeInsets.all(15),
                    //   decoration: BoxDecoration(
                    //     color: Colors.blue[50],
                    //     borderRadius: BorderRadius.circular(8),
                    //     boxShadow: [
                    //       BoxShadow(
                    //         color: Colors.black12,
                    //         blurRadius: 5,
                    //         offset: Offset(0, 2),
                    //       ),
                    //     ],
                    //   ),
                    //   child: Text(
                    //     'Share Your Location: Near Q9J2+3M9, Attock, Pakistan',
                    //     style: TextStyle(fontSize: 16),
                    //   ),
                    // ),
                    const SizedBox(height: 30),
                    // Show the "OR" divider and login button only if not logged in
                    !isLoggedIn
                        ? Column(
                          children: [
                            const SizedBox(height: 30),
                            // Divider with "OR" in the middle (This part is visible when not logged in)
                            Row(
                              children: <Widget>[
                                const Expanded(
                                  child: Divider(
                                    color: Colors.black26,
                                    thickness: 1,
                                  ),
                                ),
                                Padding(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 8.0,
                                  ),
                                  child: Text(
                                    'OR', // Label for divider (this text appears in the middle of the line)
                                    style: TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 15,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                                const Expanded(
                                  child: Divider(
                                    color: Colors.black26,
                                    thickness: 1,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 20),
                            // Login button shown when not logged in (this button is visible when the user is not logged in)
                            ElevatedButton(
                              onPressed: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => LoginPage(),
                                  ),
                                );
                              },
                              style: ElevatedButton.styleFrom(
                                minimumSize: Size(170, 40),
                              ),
                              child: Text(
                                "Login", // Label for the button (this button is for login)
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: Colors.black,
                                  fontSize: 18,
                                ),
                              ),
                            ),
                          ],
                        )
                        : SizedBox.shrink(),
                  ],
                ),
              ),
            ),
            // Conditionally show the bottom navigation bar only when the user is logged in
            bottomNavigationBar:
                isLoggedIn
                    ? BottomNavigationBar(
                      items: const [
                        BottomNavigationBarItem(
                          icon: Icon(Icons.home),
                          label: 'Home',
                        ),
                        BottomNavigationBarItem(
                          icon: Icon(Icons.location_on),
                          label: 'Location',
                        ),
                        BottomNavigationBarItem(
                          icon: Icon(Icons.phone),
                          label: 'Call',
                        ),
                        BottomNavigationBarItem(
                          icon: Icon(Icons.info),
                          label: 'Info',
                        ),
                      ],
                      backgroundColor: Colors.blueAccent,
                      selectedItemColor: Colors.red,
                      unselectedItemColor: Colors.grey,
                      selectedLabelStyle: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                      iconSize: 28,
                    )
                    : null, // If not logged in, don't show the bottom navigation bar
          ),
    );
  }
}
