<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipmentCostNotification;
use App\Models\CarEquipmentCost;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;

class EquipmentCostNotificationController extends Controller
{

    /**
     * Get equipment cost notifications for the authenticated user (admin approval interface)
     */
    public function index(Request $request)
    {
        // This will be handled by the view logic with filters
        return view('equipment-cost-notifications.index');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = EquipmentCostNotification::where('notified_user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => Auth::user()->unreadEquipmentCostNotifications()->count()
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadEquipmentCostNotifications()->update([
            'status' => 'read',
            'read_at' => now()
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
     * Get unread count for equipment cost notifications
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadEquipmentCostNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Approve equipment cost
     */
    public function approveEquipmentCost(Request $request, $costId)
    {
        $cost = CarEquipmentCost::with(['car', 'user'])->findOrFail($costId);
        
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Update cost status to approved
        $cost->update(['status' => 'approved']);

        // Create notification for the user who requested the cost
        if ($cost->user_id !== Auth::id()) {
            $notification = EquipmentCostNotification::createApprovalResponse(
                $cost->car,
                $cost,
                $cost->user,
                $cost->user,
                'approved',
                Auth::user()->name
            );
            
            // Broadcast the notification (this will be handled by the model's booted method)
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
        $cost = CarEquipmentCost::with(['car', 'user'])->findOrFail($costId);
        
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
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
            $notification = EquipmentCostNotification::createApprovalResponse(
                $cost->car,
                $cost,
                $cost->user,
                $cost->user,
                'rejected',
                Auth::user()->name
            );
            
            // Broadcast the notification (this will be handled by the model's booted method)
        }

        return response()->json([
            'success' => true,
            'message' => 'Equipment cost rejected successfully'
        ]);
    }

    /**
     * Transfer equipment cost to other costs
     */
    public function transferEquipmentCost(Request $request, $costId)
    {
        $cost = CarEquipmentCost::with(['car', 'user'])->findOrFail($costId);
        
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $transferReason = $request->input('transfer_reason', 'Transferred to other costs');

        // Update cost status to transferred
        $cost->update(['status' => 'transferred']);

        // Create notification for the user who requested the cost
        if ($cost->user_id !== Auth::id()) {
            $notification = EquipmentCostNotification::createApprovalResponse(
                $cost->car,
                $cost,
                $cost->user,
                $cost->user,
                'transferred',
                Auth::user()->name
            );
            
            // Broadcast the notification (this will be handled by the model's booted method)
        }

        return response()->json([
            'success' => true,
            'message' => 'Equipment cost transferred successfully'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = EquipmentCostNotification::where('notified_user_id', Auth::id())
            ->findOrFail($id);

        $notification->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Notification deleted successfully');
    }

    /**
     * Get notification details
     */
    public function show(Request $request, $id)
    {
        $notification = EquipmentCostNotification::where('notified_user_id', Auth::id())
            ->with(['car', 'carEquipmentCost', 'requestedByUser'])
            ->findOrFail($id);

        // Mark as read if not already read
        if ($notification->isUnread()) {
            $notification->markAsRead();
        }

        if ($request->ajax()) {
            return response()->json([
                'notification' => $notification,
                'car' => $notification->car,
                'equipment_cost' => $notification->carEquipmentCost,
                'requested_by' => $notification->requestedByUser
            ]);
        }

        return view('equipment-cost-notifications.show', compact('notification'));
    }

    /**
     * Change equipment cost description
     */
    public function changeDescription(Request $request, $costId)
    {
        $cost = CarEquipmentCost::with(['car', 'user'])->findOrFail($costId);
        
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $validated = $request->validate([
            'new_description' => 'required|string|max:255',
            'change_reason' => 'nullable|string'
        ]);

        try {
            // Update description and track history
            $cost->updateDescription($validated['new_description'], $validated['change_reason']);

            return response()->json([
                'success' => true,
                'message' => 'Description changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error changing description: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show description history for an equipment cost
     */
    public function showDescriptionHistory($costId)
    {
        $equipmentCost = CarEquipmentCost::with(['car'])
            ->findOrFail($costId);
        
        // Load description histories with user data directly
        $descriptionHistories = \App\Models\CarEquipmentCostDescriptionHistory::select([
                'car_equipment_cost_description_histories.*',
                'users.name as user_name'
            ])
            ->leftJoin('users', 'car_equipment_cost_description_histories.changed_by_user_id', '=', 'users.id')
            ->where('car_equipment_cost_id', $costId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('equipment-cost-notifications.description-history', compact('equipmentCost', 'descriptionHistories'));
    }
}
