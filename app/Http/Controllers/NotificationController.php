<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\EquipmentCostNotification;
use App\Models\InsuranceExpiryNotification;
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
        
        // Get all types of notifications
        $regularNotifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get();
            
        $equipmentCostNotifications = $user->equipmentCostNotifications()
            ->with(['car', 'carEquipmentCost', 'requestedByUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        $insuranceExpiryNotifications = $user->insuranceExpiryNotifications()
            ->with(['car'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Combine and sort by created_at
        $allNotifications = $regularNotifications->concat($equipmentCostNotifications)
            ->concat($insuranceExpiryNotifications)
            ->sortByDesc('created_at')
            ->values();
        
        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        $offset = ($page - 1) * $perPage;
        $paginatedNotifications = $allNotifications->slice($offset, $perPage);
        
        $totalUnread = $user->unreadNotifications()->count() + 
                      $user->unreadEquipmentCostNotifications()->count() + 
                      $user->unreadInsuranceExpiryNotifications()->count();

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $paginatedNotifications->values(),
                'unread_count' => $totalUnread,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => ceil($allNotifications->count() / $perPage),
                    'per_page' => $perPage,
                    'total' => $allNotifications->count()
                ]
            ]);
        }

        return view('notifications.index', compact('paginatedNotifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        
        // Try to find in regular notifications first
        $notification = Notification::where('user_id', $user->id)
            ->find($id);
            
        if (!$notification) {
            // Try to find in equipment cost notifications
            $notification = EquipmentCostNotification::where('notified_user_id', $user->id)
                ->findOrFail($id);
            $notification->markAsRead();
        } elseif (!$notification) {
            // Try to find in insurance expiry notifications
            $notification = InsuranceExpiryNotification::where('user_id', $user->id)
                ->findOrFail($id);
            $notification->markAsRead();
        } else {
            $notification->markAsRead();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $user->unreadNotifications()->count() + 
                                $user->unreadEquipmentCostNotifications()->count() + 
                                $user->unreadInsuranceExpiryNotifications()->count()
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        // Mark all regular notifications as read
        $user->unreadNotifications()->update(['read_at' => \Carbon\Carbon::now()]);
        
        // Mark all equipment cost notifications as read
        $user->unreadEquipmentCostNotifications()->update([
            'status' => 'read',
            'read_at' => \Carbon\Carbon::now()
        ]);

        // Mark all insurance expiry notifications as read
        $user->unreadInsuranceExpiryNotifications()->update([
            'status' => 'read',
            'read_at' => \Carbon\Carbon::now()
        ]);

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
        $user = Auth::user();
        $count = $user->unreadNotifications()->count() + 
                $user->unreadEquipmentCostNotifications()->count() + 
                $user->unreadInsuranceExpiryNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }


}
