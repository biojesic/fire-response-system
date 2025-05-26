class User {
  final int id;
  final String? firstName;
  final String? lastName;
  final String? email;
  final String? address;
  final String? contactNumber;
  final String? birthDate;

  User({
    required this.id,
    this.firstName,
    this.lastName,
    this.email,
    this.address,
    this.contactNumber,
    this.birthDate,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: int.parse(json['id'].toString()),
      firstName: json['userFirstName'] ?? '',
      lastName: json['userLastName'] ?? '',
      email: json['email'] ?? '',
      address: json['userAddress'] ?? '',
      contactNumber: json['userContactNumber'] ?? '',
      birthDate: json['userBirthDate'] ?? '',
    );
  }

  String? get userFirstName => firstName;

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'userFirstName': firstName ?? '',
      'userLastName': lastName ?? '',
      'email': email ?? '',
      'userAddress': address ?? '',
      'userContactNumber': contactNumber ?? '',
      'userBirthDate': birthDate ?? '',
    };
  }
}
