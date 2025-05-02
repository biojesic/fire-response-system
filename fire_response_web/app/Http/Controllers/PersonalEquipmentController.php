<?php

namespace App\Http\Controllers;

use App\Models\PersonalEquipment;
use Illuminate\Http\Request;

class PersonalEquipmentController extends Controller
{
    // Display a listing of the equipment
    public function index(Request $request) {
        // Query to fetch equipment, with optional search functionality
        $query = PersonalEquipment::query();
        
        // 🔍 Apply search if filled
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $equipments = $query->paginate(10)->withQueryString();

        return view('admin_pages.equipments', compact('equipments'));
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
            'serial_number' => 'nullable|string|max:255',
            'quantities' => 'required|integer|min:1',
        ]);

        PersonalEquipment::create([
            'name' => $request->name,
            'serial_number' => $request->serial_number,
            'quantities' => $request->quantities,  // Save the quantity
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

