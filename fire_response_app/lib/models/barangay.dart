class Barangay {
  final int id;
  final String barangay_name;

  Barangay({required this.id, required this.barangay_name});

  factory Barangay.fromJson(Map<String, dynamic> json) {
    return Barangay(id: json['id'], barangay_name: json['barangay_name']);
  }

  Map<String, dynamic> toJson() => {'id': id, 'barangay_name': barangay_name};
}
