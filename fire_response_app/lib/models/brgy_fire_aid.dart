// import 'dart:convert';
import 'user.dart';
import 'barangay.dart';

class FireAid {
  final int id;
  final int userId;
  final int barangayId;
  final String? status;
  final String? photo;
  final String? barangayIdPath;
  final String? barangayCertificatePath;
  final User? user;
  final Barangay? barangay;

  FireAid({
    required this.id,
    required this.userId,
    required this.barangayId,
    this.status,
    this.photo,
    this.barangayIdPath,
    this.barangayCertificatePath,
    this.user,
    this.barangay,
  });

  // Factory constructor to create a FireAid instance from JSON
  factory FireAid.fromJson(Map<String, dynamic> json) {
    return FireAid(
      id: json['id'],
      userId: json['user_id'],
      barangayId: json['barangay_id'],
      status: json['status'],
      photo: json['photo'],
      barangayIdPath: json['barangay_id_path'],
      barangayCertificatePath: json['barangay_certificate_path'],
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      barangay:
          json['barangay'] != null ? Barangay.fromJson(json['barangay']) : null,
    );
  }
}
