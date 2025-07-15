@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <h3 class="kt-card-title">Notifications</h3>
    </div>
    <div class="kt-card-content">
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="notification-item p-4 border border-gray-200 rounded-lg {{ $notification->isRead() ? 'opacity-60' : '' }}">
                        <div class="flex items-start gap-3">
                            @if(!$notification->isRead())
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            @endif
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $notification->title }}</h4>
                                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                
                                @if($notification->type === 'equipment_cost_approval_requested' && !$notification->isRead())
                                    <div class="flex gap-2 mt-2">
                                        <button class="kt-btn kt-btn-sm kt-btn-success approve-cost-btn" 
                                                data-cost-id="{{ $notification->data['cost_id'] ?? '' }}">
                                            <i class="ki-filled ki-check"></i>
                                            Approve
                                        </button>
                                        <button class="kt-btn kt-btn-sm kt-btn-danger reject-cost-btn" 
                                                data-cost-id="{{ $notification->data['cost_id'] ?? '' }}">
                                            <i class="ki-filled ki-cross"></i>
                                            Reject
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                <h4 class="text-lg font-bold text-gray-900 mb-2">No notifications</h4>
                <p class="text-gray-600">You don't have any notifications yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection 