<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        $targetUrl = $notification->resolved_url ?? $notification->related_url;

        if ($targetUrl) {
            return redirect($targetUrl);
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }
}
