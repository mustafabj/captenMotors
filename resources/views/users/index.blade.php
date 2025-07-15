@extends('layouts.app')

@section('content')
    <div class="grid w-full space-y-5">
        <!-- Header with Search and Actions -->
        <div class="kt-card">
            <div class="kt-card-body">
                <div class="flex flex-col lg:flex-row gap-4 lg:items-start lg:items-center justify-between p-4">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <div class="relative flex">
                            <input type="text" id="search-input" placeholder="Search users..." class="kt-input w-full pl-10" />
                            <i class="ki-filled ki-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Role Filter -->
                        <select id="role-filter" class="kt-select kt-select-sm">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="user">User</option>
                        </select>

                        <!-- Add User Button -->
                        <a href="{{ route('users.create') }}" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-plus"></i>
                            Add User
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
            </div>
        </div>

        <!-- Users Table -->
        <div id="users-container" data-search-url="{{ route('users.search') }}">
            @include('users.partials.user-table', ['users' => $users])
        </div>

        <!-- Pagination Container -->
        <div id="pagination-container">
            @include('users.partials.pagination', ['users' => $users])
        </div>
    </div>
@endsection 