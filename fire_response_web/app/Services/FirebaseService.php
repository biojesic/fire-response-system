<?php

namespace App\Services;

use Kreait\Firebase\Factory; 
use Kreait\Firebase\Messaging\CloudMessage;
use App\Services\FirebaseService;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;

class FirebaseService {

    protected $messaging;

    public function __construct() {

        $serviceAccountpath = storage_path('fire-response-app-cfab4-firebase-adminsdk-fbsvc-48ccc2f9dc.json');

        $factory = (new Factory)->withServiceAccount($serviceAccountpath);
        $this->messaging = $factory->createMessaging();

    }

    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        // Log the token and payload for debugging purposes
        Log::info("Sending notification to token: $fcmToken");
        Log::info("Notification details: Title: $title, Body: $body");

        // Create the message
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data)  // Attach any additional data (optional)
            ->withTarget('token', $fcmToken);  // Correct method to target the FCM token

        // Send the message
        try {
            $response = $this->messaging->send($message);  // Send the notification
            Log::info("Notification sent successfully to token: $fcmToken");
            Log::info("Firebase response: " . json_encode($response));
        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
        }
    }
}