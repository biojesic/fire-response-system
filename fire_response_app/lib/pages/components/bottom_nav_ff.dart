import 'package:flutter/material.dart';

class BottomNavFF extends StatelessWidget {
  final int currentIndex;
  final Function(int) onTap;

  const BottomNavFF({Key? key, required this.currentIndex, required this.onTap})
    : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BottomNavigationBar(
      currentIndex: currentIndex,
      onTap: onTap,
      backgroundColor: Colors.white, // Change this to any color you want
      selectedItemColor: Colors.red[600], // Color of selected item
      unselectedItemColor: Colors.black38, // Color of unselected items
      selectedLabelStyle: TextStyle(),
      items: const [
        BottomNavigationBarItem(
          icon: Icon(Icons.home_rounded, size: 40),
          label: 'Home',
        ),
        BottomNavigationBarItem(
          icon: Icon(Icons.insert_drive_file_outlined, size: 40),
          label: 'Reports',
        ),
        BottomNavigationBarItem(
          icon: Icon(Icons.settings, size: 40),
          label: 'Settings',
        ),
      ],
    );
  }
}
