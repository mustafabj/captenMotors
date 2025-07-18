@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Notifications</h3>
        </div>
        <div class="kt-card-toolbar">
            <button id="mark-all-read-btn" class="kt-btn kt-btn-sm kt-btn-secondary">
                <i class="ki-filled ki-check-double"></i>
                Mark All as Read
            </button>
        </div>
    </div>
    <div class="kt-card-body">
        <div id="notifications-list">
            <!-- Notifications will be loaded here -->
        </div>
    </div>
</div>
@endsection 