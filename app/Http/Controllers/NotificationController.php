<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use App\Services\NotifixService;

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
                $notifications[] = [
                    'id' => $note['id'],
                    'message' => $note['message'],
                    'type' => $note['type'],
                    'link_to' => $note['link_to'] ?? '/notifications',
                    'created_date' => $note['created_date'],
                    'time_ago' => \Carbon\Carbon::parse($note['created_date'])->diffForHumans(),
                ];
            }
        }
    
        // Artisan::call('notifications:send-anniversaries');
        return view('notifications.index', ['notifications' => $notifications]);
    }
}
