class User {
  final int id;
  final String firstName;
  final String lastName;
  final String email;
  final String address;
  final String contactNumber;

  User({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.address,
    required this.contactNumber,
  });

  // Convert JSON data from API to User model
  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      firstName: json['userFirstName'],
      lastName: json['userLastName'],
      email: json['userEmail'],
      address: json['userAddress'],
      contactNumber: json['userContactNumber'],
    );
  }

  // Convert User model to JSON (optional)
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'userFirstName': firstName,
      'userLastName': lastName,
      'userEmail': email,
      'userAddress': address,
      'userContactNumber': contactNumber,
    };
  }
}
