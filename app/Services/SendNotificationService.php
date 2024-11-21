<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;
class SendNotificationService
{
    public static function sendNotification($title, $body, $deviceToken)
    {
        try {
            $firebase = (new Factory())->withServiceAccount(base_path('test-d7f66-firebase-adminsdk-unj0s-a7d3c8c078.json'));
            $messaging = $firebase->createMessaging();

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ]);

            $messaging->send($message);

            Log::info('Notification sent successfully to device token: ' . $deviceToken);

            return json_encode([
                'status' => 'success',
                'message' => 'Notification sent successfully',
            ]);
        } catch (MessagingException $e) {
            Log::error('Firebase MessagingException: ' . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'message' => 'Firebase MessagingException: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'message' => 'General Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
