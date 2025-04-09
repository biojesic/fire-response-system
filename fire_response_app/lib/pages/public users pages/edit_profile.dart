import 'package:fire_response_app/provider/user_provider.dart';
import 'package:flutter/material.dart';
import 'package:fire_response_app/models/user.dart';
import 'package:provider/provider.dart';

class EditProfile extends StatefulWidget {
  final User user;

  const EditProfile({super.key, required this.user});

  @override
  State<EditProfile> createState() => _EditProfileState();
}

class _EditProfileState extends State<EditProfile> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _firstNameController;
  late TextEditingController _lastNameController;
  late TextEditingController _emailController;
  late TextEditingController _addressController;
  late TextEditingController _contactController;

  @override
  void initState() {
    super.initState();
    // Get the current user data from the provider
    final user = Provider.of<UserProvider>(context, listen: false).currentUser;

    // Initialize text controllers with current user data
    _firstNameController = TextEditingController(text: user?.firstName ?? '');
    _lastNameController = TextEditingController(text: user?.lastName ?? '');
    _emailController = TextEditingController(text: user?.email ?? '');
    _addressController = TextEditingController(text: user?.address ?? '');
    _contactController = TextEditingController(text: user?.contactNumber ?? '');
  }

  void _saveChanges() {
    if (_formKey.currentState!.validate()) {
      final updatedUser = User(
        id:
            Provider.of<UserProvider>(context, listen: false).currentUser?.id ??
            0,
        firstName: _firstNameController.text,
        lastName: _lastNameController.text,
        email: _emailController.text,
        address: _addressController.text,
        contactNumber: _contactController.text,
      );

      // Save updated user using the provider
      Provider.of<UserProvider>(context, listen: false).updateUser(updatedUser);

      // Pop the edit page and return to profile page
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Edit Profile')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              TextFormField(
                controller: _firstNameController,
                decoration: InputDecoration(labelText: 'First Name'),
                validator:
                    (value) => value!.isEmpty ? 'Enter first name' : null,
              ),
              SizedBox(height: 12),
              TextFormField(
                controller: _lastNameController,
                decoration: InputDecoration(labelText: 'Last Name'),
                validator: (value) => value!.isEmpty ? 'Enter last name' : null,
              ),
              SizedBox(height: 12),
              TextFormField(
                controller: _emailController,
                decoration: InputDecoration(labelText: 'Email'),
                validator: (value) => value!.isEmpty ? 'Enter email' : null,
              ),
              SizedBox(height: 12),
              TextFormField(
                controller: _addressController,
                decoration: InputDecoration(labelText: 'Address'),
                validator: (value) => value!.isEmpty ? 'Enter address' : null,
              ),
              SizedBox(height: 12),
              TextFormField(
                controller: _contactController,
                decoration: InputDecoration(labelText: 'Contact Number'),
                validator:
                    (value) => value!.isEmpty ? 'Enter contact number' : null,
              ),
              SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  showDialog(
                    context: context,
                    builder:
                        (context) => AlertDialog(
                          title: Text('Save Changes?'),
                          // content: Text(
                          //   'Are you sure you want to change your password?',
                          // ),
                          actions: [
                            TextButton(
                              onPressed: () {
                                Navigator.pop(
                                  context,
                                ); // ❌ Cancel - close dialog
                              },
                              child: Text('Cancel'),
                            ),
                            TextButton(
                              onPressed: () {
                                _saveChanges();
                                Navigator.pop(
                                  context,
                                ); // ✅ Close dialog and return to profile page
                              },
                              child: Text('Yes'),
                            ),
                          ],
                        ),
                  );
                },
                child: Padding(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  child: Text('Save Changes', style: TextStyle(fontSize: 18)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
