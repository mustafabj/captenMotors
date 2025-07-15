<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\CarEquipmentCost;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $user->unreadNotifications()->count(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total()
                ]
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => Auth::user()->unreadNotifications()->count()
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get unread count for notifications
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Approve equipment cost
     */
    public function approveEquipmentCost(Request $request, $costId)
    {
        $cost = CarEquipmentCost::findOrFail($costId);
        
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Update cost status to approved
        $cost->update(['status' => 'approved']);

        // Create notification for the user who requested the cost
        if ($cost->user_id !== Auth::id()) {
            Notification::create([
                'user_id' => $cost->user_id,
                'type' => 'equipment_cost_approved',
                'title' => 'Equipment Cost Approved',
                'message' => "Your equipment cost request for {$cost->description} has been approved by " . Auth::user()->name,
                'data' => [
                    'car_id' => $cost->car_id,
                    'cost_id' => $cost->id,
                    'approved_by' => Auth::user()->name
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Equipment cost approved successfully'
        ]);
    }

    /**
     * Reject equipment cost
     */
    public function rejectEquipmentCost(Request $request, $costId)
    {
        $cost = CarEquipmentCost::findOrFail($costId);
        
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $reason = $request->input('reason', 'No reason provided');

        // Update cost status to rejected
        $cost->update(['status' => 'rejected']);

        // Create notification for the user who requested the cost
        if ($cost->user_id !== Auth::id()) {
            Notification::create([
                'user_id' => $cost->user_id,
                'type' => 'equipment_cost_rejected',
                'title' => 'Equipment Cost Rejected',
                'message' => "Your equipment cost request for {$cost->description} has been rejected. Reason: {$reason}",
                'data' => [
                    'car_id' => $cost->car_id,
                    'cost_id' => $cost->id,
                    'rejected_by' => Auth::user()->name,
                    'reason' => $reason
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Equipment cost rejected successfully'
        ]);
    }
}
