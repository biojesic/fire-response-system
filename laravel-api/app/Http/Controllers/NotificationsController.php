<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        // Get all notifications
        return Notifications::with('user')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|in:fire_alert,dispatch_notice',
        ]);

        // Create a new notification
        $notification = Notifications::create($request->all());

        return response()->json($notification, 201);
    }

    public function show($id)
    {
        // Get a single notification
        return Notifications::with('user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'read' => 'required|boolean',
        ]);

        $notification = Notifications::findOrFail($id);
        $notification->update(['read' => $request->read]);

        return response()->json($notification);
    }

    public function destroy($id)
    {
        // Delete the notification
        $notification = Notifications::findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
