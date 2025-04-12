import 'package:fire_response_app/pages/auth%20pages/login.dart';
import 'package:fire_response_app/pages/components/bottom_nav_ff.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_fire_reports.dart';
import 'package:fire_response_app/pages/firefighters%20pages/firefighters_home.dart';
import 'package:fire_response_app/pages/public%20users%20pages/change_password.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:fire_response_app/provider/user_provider.dart';
import 'package:provider/provider.dart';
import 'package:flutter/material.dart';
import 'package:fire_response_app/models/user.dart';

class FirefightersSettings extends StatefulWidget {
  const FirefightersSettings({super.key});

  @override
  State<FirefightersSettings> createState() => _FirefightersSettingsState();
}

class _FirefightersSettingsState extends State<FirefightersSettings> {
  bool notif = true;
  bool theme = false;
  final int selectedIndex = 2;

  @override
  void initState() {
    super.initState();
    final userProvider = Provider.of<UserProvider>(context, listen: false);

    // KUNIN ANG TOKEN MULA SA AUTH PROVIDER
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final userToken =
        authProvider.token; // Siguraduhin kung saan mo sinesave ang token

    if (userToken != null) {
      userProvider.fetchUserData(userToken); // Huwag na gumamit ng user ID
    } else {
      print("No token found. User not authenticated.");
    }
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
    final userProvider = Provider.of<UserProvider>(context);
    User? user = userProvider.currentUser;

    return Scaffold(
      appBar: AppBar(title: Text('Settings')),
      body:
          user == null
              ? Center(
                child: CircularProgressIndicator(),
              ) // Show loader until data loads
              : SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Icon(Icons.person, size: 30),
                        SizedBox(width: 7),
                        Text(
                          'User Profile',
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ],
                    ),
                    SizedBox(height: 15),
                    Center(
                      child: ClipOval(
                        child: Image.asset(
                          'lib/images/pfp.jpg',
                          width: 100.0,
                          height: 100.0,
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    SizedBox(height: 20),
                    Center(
                      child: Text(
                        '${user.firstName} ${user.lastName}',
                        style: TextStyle(fontSize: 24),
                      ),
                    ),
                    SizedBox(height: 20),
                    Text(
                      'Email: ${user.email}',
                      style: TextStyle(fontSize: 18),
                    ),
                    Text(
                      'Address: ${user.address}',
                      style: TextStyle(fontSize: 18),
                    ),
                    Text(
                      'Contact Number: ${user.contactNumber}',
                      style: TextStyle(fontSize: 18),
                    ),
                    SizedBox(height: 5),
                    Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            InkWell(
                              onTap: () {},
                              child: Padding(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 4,
                                ),
                                child: Text(
                                  'Edit Profile',
                                  style: TextStyle(
                                    decoration: TextDecoration.underline,
                                    fontSize: 18,
                                    fontWeight: FontWeight.w500,
                                    color: Colors.black,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            InkWell(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => ChangePassword(),
                                  ),
                                );
                              },
                              child: Padding(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 4,
                                ),
                                child: Text(
                                  'Change Password',
                                  style: TextStyle(
                                    decoration: TextDecoration.underline,
                                    fontSize: 18,
                                    fontWeight: FontWeight.w500,
                                    color: Colors.black,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),

                    const SizedBox(height: 10),
                    const Divider(),
                    const SizedBox(height: 10),
                    const Row(
                      children: [
                        Icon(Icons.settings, size: 30),
                        SizedBox(width: 7),
                        Text(
                          'Settings',
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 15),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Row(
                          children: [
                            Icon(
                              Icons.notifications,
                              color: Colors.black,
                              size: 22,
                            ),
                            SizedBox(width: 6),
                            Text(
                              'Notifications',
                              style: TextStyle(fontSize: 22),
                            ),
                          ],
                        ),
                        Transform.scale(
                          scale: 0.8,
                          child: Switch(
                            value: notif,
                            onChanged: (bool value) {
                              setState(() {
                                notif = value;
                              });
                            },
                          ),
                        ),
                      ],
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Row(
                          children: [
                            Icon(
                              Icons.brightness_4_outlined,
                              color: Colors.black,
                              size: 22,
                            ),
                            SizedBox(width: 6),
                            Text('Theme', style: TextStyle(fontSize: 22)),
                          ],
                        ),
                        Transform.scale(
                          scale: 0.8,
                          child: Switch(
                            value: theme,
                            onChanged: (bool value) {
                              setState(() {
                                theme = value;
                              });
                            },
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 50),
                    Center(
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.red[600],
                          padding: const EdgeInsets.symmetric(
                            horizontal: 30,
                            vertical: 12,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                        onPressed: () async {
                          bool confirmLogout = await showDialog(
                            context: context,
                            builder: (BuildContext context) {
                              return AlertDialog(
                                title: const Text("Confirm Logout"),
                                content: const Text(
                                  "Are you sure you want to log out?",
                                ),
                                actions: [
                                  TextButton(
                                    onPressed: () {
                                      Navigator.of(
                                        context,
                                      ).pop(false); // Cancel logout
                                    },
                                    child: const Text("Cancel"),
                                  ),
                                  TextButton(
                                    onPressed: () {
                                      Navigator.of(
                                        context,
                                      ).pop(true); // Confirm logout
                                    },
                                    child: const Text(
                                      "Logout",
                                      style: TextStyle(color: Colors.red),
                                    ),
                                  ),
                                ],
                              );
                            },
                          );

                          if (confirmLogout == true) {
                            final authProvider = Provider.of<AuthProvider>(
                              context,
                              listen: false,
                            );
                            await authProvider.logout();

                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text("Logged out successfully"),
                                duration: Duration(seconds: 2),
                              ),
                            );

                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const LoginPage(),
                              ),
                            );
                          }
                        },
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.exit_to_app_rounded,
                              color: Colors.white,
                            ),
                            SizedBox(width: 10),
                            Text(
                              'Logout',
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
      bottomNavigationBar: BottomNavFF(
        currentIndex: selectedIndex,
        onTap: onItemTapped,
      ),
    );
  }
}
