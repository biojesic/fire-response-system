import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:fire_response_app/provider/firefighter_reports_provider.dart';

class FinalRealTimeReport extends StatefulWidget {
  final int fireReportId; // Declare a variable to hold the fireReportId

  FinalRealTimeReport({
    required this.fireReportId,
  }); // Accept fireReportId in the constructor

  @override
  State<FinalRealTimeReport> createState() => _FinalRealTimeReportState();
}

class _FinalRealTimeReportState extends State<FinalRealTimeReport> {
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Final Real Time Report'),
        backgroundColor: Colors.transparent, // Transparent background
        elevation: 0, // No shadow
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios, color: Colors.black), // Back arrow
          onPressed: () {
            Navigator.pop(context); // Go back to the previous screen
          },
        ),
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: EdgeInsets.fromLTRB(20, 35, 20, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              buildTextField('Incident Type', incidenttypeController),
              SizedBox(height: 15),
              buildTextField('Involved', involvedController),
              SizedBox(height: 15),
              buildTextField('Name of Owner', nameofownerController),
              SizedBox(height: 15),
              buildTextField('Estimated Damage', estimateddamageController),
              SizedBox(height: 15),
              buildTextField('Fatality', fatalityController),
              SizedBox(height: 15),
              buildTextField('Injured', injuredController),
              SizedBox(height: 15),
              buildTextField(
                'Number of Houses/Establishments',
                numofhousesController,
              ),
              SizedBox(height: 15),
              buildTextField(
                'Number of Families Affected',
                numoffamiliesController,
              ),
              SizedBox(height: 15),
              buildTextField(
                'Number of Fire Truck Responded',
                numoftruckController,
              ),
              SizedBox(height: 15),
              buildTextField('Ground Commander', groundcommanderController),
              SizedBox(height: 15),
              buildTextField('Alarm Status', alarmstatusController),
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
                      shape: MaterialStateProperty.all<RoundedRectangleBorder>(
                        RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8.0),
                        ),
                      ),
                    ),
                    onPressed: () {
                      // Collect the data from the form fields
                      final Map<String, String> body = {
                        'incident_type': involvedController.text,
                        'involved': involvedController.text,
                        'name_of_owner': involvedController.text,
                        'alarm_status': involvedController.text,
                        'estimated_damage': involvedController.text,
                        'fatality': involvedController.text,
                        'injured': involvedController.text,
                        'number_of_houses_establishments':
                            numofhousesController.text,
                        'number_of_families_affected':
                            numoffamiliesController.text,
                        'number_of_firetrucks': numoftruckController.text,
                      };

                      // Call submitFinalReport from the provider
                      Provider.of<FirefighterReportsProvider>(
                        context,
                        listen: false,
                      ).submitFinalReport(context, widget.fireReportId, body);
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
    );
  }

  // Helper function to avoid code repetition
  Widget buildTextField(String label, TextEditingController controller) {
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
            decoration: const InputDecoration(border: InputBorder.none),
            style: TextStyle(color: Colors.black),
            cursorColor: Colors.black,
          ),
        ),
      ],
    );
  }
}
