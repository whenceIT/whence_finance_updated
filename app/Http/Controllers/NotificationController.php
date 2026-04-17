<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use App\Services\NotifixService;
use Illuminate\Support\Facades\Artisan;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifixService = app(NotifixService::class);
        $notifix = $notifixService->getMyNotifix($user->id);

        $notifications = [];
        if ($notifix && $notifix->note) {
            // Sort by created_date desc and only show unread notifications
            $notes = collect($notifix->note)->sortByDesc('created_date')->take(20)->values()->all();
            foreach ($notes as $note) {
                $notification = [
                    'id' => $note['id'],
                    'message' => $note['message'],
                    'type' => $note['type'],
                    'link_to' => $note['link_to'] ?? '/notifications',
                    'created_date' => $note['created_date'],
                    'time_ago' => \Carbon\Carbon::parse($note['created_date'])->diffForHumans(),
                ];

                // Add upload poster for training recommendations
                if ($note['type'] === 'training_recommendation' && isset($note['upload_poster'])) {
                    $notification['upload_poster'] = $note['upload_poster'];
                }

                $notifications[] = $notification;
            }
        }
    
        // Artisan::call('notifications:send-anniversaries');
        return view('notifications.index', ['notifications' => $notifications]);
    }

    public function delete($notificationId)
    {
        if (!Sentinel::getUser()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifixService = app(NotifixService::class);
        $result = $notifixService->removeNotification(Sentinel::getUser()->id, $notificationId);

        return response()->json(['success' => $result]);
    }

    public function runScheduledCommands()
    {
        if (!Sentinel::getUser()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            Artisan::call('notifications:send-training-links');
            Artisan::call('notifications:send-overdue-clients');

            return response()->json(['success' => true, 'message' => 'Commands executed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
