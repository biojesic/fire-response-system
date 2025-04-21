<?php

namespace App\Http\Controllers;

use App\Models\PersonalEquipment;
use Illuminate\Http\Request;

class PersonalEquipmentController extends Controller
{
    // Display a listing of the equipment
    public function index()
    {
        $equipment = PersonalEquipment::all();
        // return view('admin.pages.equipment_list', compact('equipment'));
    }

    // Show the form for creating a new equipment
    public function create()
    {
        // return view('admin.pages.add_equipment');
    }

    // Store a newly created equipment in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:personal_equipment',
        ]);

        PersonalEquipment::create([
            'name' => $request->name,
        ]);

        // return redirect()->route('admin.equipment.list')->with('success', 'Equipment added successfully.');
    }

    // Show the form for editing the specified equipment
    public function edit($id)
    {
        // $equipment = PersonalEquipment::findOrFail($id);
        // return view('admin.pages.edit_equipment', compact('equipment'));
    }

    // Update the specified equipment in the database
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255|unique:personal_equipment,name,' . $id,
        // ]);

        // $equipment = PersonalEquipment::findOrFail($id);
        // $equipment->update([
        //     'name' => $request->name,
        // ]);

        // return redirect()->route('admin.equipment.list')->with('success', 'Equipment updated successfully.');
    }

    // Remove the specified equipment from the database
    public function destroy($id)
    {
        // $equipment = PersonalEquipment::findOrFail($id);
        // $equipment->delete();

        // return redirect()->route('admin.equipment.list')->with('success', 'Equipment deleted successfully.');
    }
}

