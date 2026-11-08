<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display the current user's notification history.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Return the unread count used by the lightweight notification-bell poll.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark one notification as read.
     */
    public function read(Request $request, string $notification): RedirectResponse
    {
        /** @var DatabaseNotification $databaseNotification */
        $databaseNotification = $request->user()
            ->notifications()
            ->findOrFail($notification);

        if (is_null($databaseNotification->read_at)) {
            $databaseNotification->markAsRead();
        }

        return back();
    }

    /**
     * Mark every unread notification belonging to the current user as read.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back();
    }
}
