@extends('layouts.app')

@section('content')
    <div class="grid w-full space-y-5">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                <p class="text-sm text-gray-500">Update user information</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="kt-btn kt-btn-outline">
                    Cancel
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
            </div>
            <div class="kt-card-content p-5">
                <form id="user-form" action="{{ route('users.update', $user) }}" method="POST" class="space-y-5" data-user-id="{{ $user->id }}">
                    @csrf
                    @method('PUT')

                    <!-- General Error Messages -->
                    <div id="general-errors" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="ki-filled ki-information-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                <div id="error-list" class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                class="kt-input w-full" placeholder="Enter full name">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                class="kt-input w-full" placeholder="Enter email address">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" id="password"
                                class="kt-input w-full" placeholder="Leave blank to keep current password">
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                            <select name="role" id="role" required class="kt-select"
                            data-kt-select="true" data-kt-select-placeholder="Select Role">
                                <option value="">Select a role</option>
                                <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" class="kt-btn kt-btn-outline">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            <span class="btn-text">Update User</span>
                            <span class="btn-loading hidden">
                                <i class="ki-filled ki-loading animate-spin"></i>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 