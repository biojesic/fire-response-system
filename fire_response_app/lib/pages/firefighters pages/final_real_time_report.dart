import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/provider/firefighter_reports_provider.dart';

class FinalRealTimeReport extends StatefulWidget {
  final int fireReportId;

  FinalRealTimeReport({required this.fireReportId});

  @override
  State<FinalRealTimeReport> createState() => _FinalRealTimeReportState();
}

class _FinalRealTimeReportState extends State<FinalRealTimeReport> {
  final _formKey = GlobalKey<FormState>();

  TextEditingController involvedController = TextEditingController();
  TextEditingController nameofownerController = TextEditingController();
  TextEditingController estimateddamageController = TextEditingController();
  TextEditingController fatalityController = TextEditingController();
  TextEditingController injuredController = TextEditingController();
  TextEditingController numofhousesController = TextEditingController();
  TextEditingController numoffamiliesController = TextEditingController();
  TextEditingController numoftruckController = TextEditingController();
  TextEditingController groundcommanderController = TextEditingController();
  TextEditingController alarmstatusController = TextEditingController();
  TextEditingController incidenttypeController = TextEditingController();
  TextEditingController timeofarrivalController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Final Real Time Report'),
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios, color: Colors.black),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: EdgeInsets.fromLTRB(20, 35, 20, 20),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                buildTextField(
                  'Incident Type',
                  incidenttypeController,
                  validator: _requiredValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Involved',
                  involvedController,
                  validator: _requiredValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Name of Owner',
                  nameofownerController,
                  validator: _requiredValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Estimated Damage',
                  estimateddamageController,
                  validator: _numericValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Fatality',
                  fatalityController,
                  validator: _numericValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Injured',
                  injuredController,
                  validator: _numericValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Number of Houses/Establishments',
                  numofhousesController,
                  validator: _numericValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Number of Families Affected',
                  numoffamiliesController,
                  validator: _numericValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Number of Fire Truck Responded',
                  numoftruckController,
                  validator: _numericValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Ground Commander',
                  groundcommanderController,
                  validator: _requiredValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Time of Arrival',
                  timeofarrivalController,
                  validator: _timeValidator,
                ),
                SizedBox(height: 15),
                buildTextField(
                  'Alarm Status',
                  alarmstatusController,
                  validator: _requiredValidator,
                ),
                SizedBox(height: 35),
                Center(
                  child: SizedBox(
                    width: 220,
                    height: 55,
                    child: ElevatedButton(
                      style: ButtonStyle(
                        backgroundColor: MaterialStateProperty.all<Color>(
                          Colors.green,
                        ),
                        shape:
                            MaterialStateProperty.all<RoundedRectangleBorder>(
                              RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8.0),
                              ),
                            ),
                      ),
                      onPressed: () {
                        if (_formKey.currentState!.validate()) {
                          final Map<String, String> body = {
                            'incident_type': incidenttypeController.text,
                            'involved': involvedController.text,
                            'name_of_owner': nameofownerController.text,
                            'alarm_status': alarmstatusController.text,
                            'estimated_damage': estimateddamageController.text,
                            'fatality': fatalityController.text,
                            'injured': injuredController.text,
                            'number_of_houses_establishments':
                                numofhousesController.text,
                            'number_of_families_affected':
                                numoffamiliesController.text,
                            'number_of_firetrucks': numoftruckController.text,
                            'time_of_arrival': timeofarrivalController.text,
                            'ground_commander': groundcommanderController.text,
                          };

                          Provider.of<FirefighterReportsProvider>(
                            context,
                            listen: false,
                          ).submitFinalReport(
                            context,
                            widget.fireReportId,
                            body,
                          );
                        }
                      },
                      child: Text(
                        'Submit Final Report',
                        style: TextStyle(
                          fontFamily: GoogleFonts.poppins().fontFamily,
                          fontSize: 16,
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget buildTextField(
    String label,
    TextEditingController controller, {
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: TextStyle(
            fontFamily: GoogleFonts.poppins().fontFamily,
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
        SizedBox(height: 5),
        Container(
          height: 40,
          decoration: BoxDecoration(
            color: Colors.grey.shade100,
            boxShadow: [
              BoxShadow(
                color: const Color.fromARGB(255, 207, 190, 190),
                blurRadius: 10,
                offset: Offset(3, 3),
              ),
            ],
          ),
          child: TextFormField(
            controller: controller,
            validator: validator,
            decoration: const InputDecoration(
              border: InputBorder.none,
              contentPadding: EdgeInsets.symmetric(horizontal: 10),
            ),
            style: TextStyle(color: Colors.black),
            cursorColor: Colors.black,
          ),
        ),
      ],
    );
  }

  String? _requiredValidator(String? value) {
    return (value == null || value.trim().isEmpty)
        ? 'This field is required'
        : null;
  }

  String? _numericValidator(String? value) {
    if (value == null || value.trim().isEmpty) return 'This field is required';
    return double.tryParse(value) == null ? 'Enter a valid number' : null;
  }

  String? _timeValidator(String? value) {
    if (value == null || value.trim().isEmpty)
      return 'Time of arrival is required';
    final timeRegex = RegExp(r'^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$');
    return !timeRegex.hasMatch(value) ? 'Use format HH:mm:ss' : null;
  }
}
