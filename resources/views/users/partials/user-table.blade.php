<div class="kt-card">
    <div class="kt-card-body p-0">
        <!-- Desktop Table -->
        <div class="hidden lg:block">
            <div class="kt-scrollable-x-auto">
                <table class="kt-table kt-table-border w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-sm font-semibold text-white">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->hasRole('admin'))
                                        <span class="kt-badge kt-badge-primary">Admin</span>
                                    @elseif($user->hasRole('manager'))
                                        <span class="kt-badge kt-badge-info">Manager</span>
                                    @else
                                        <span class="kt-badge kt-badge-secondary">User</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->trashed())
                                        <span class="kt-badge kt-badge-destructive">Deleted</span>
                                    @else
                                        <span class="kt-badge kt-badge-success">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($user->trashed())
                                            <!-- Restore Button -->
                                            <button onclick="restoreUser({{ $user->id }})" 
                                                    class="kt-btn kt-btn-sm kt-btn-outline kt-btn-primary"
                                                    title="Restore User">
                                                <i class="ki-filled ki-arrow-up"></i>
                                            </button>
                                            
                                            <!-- Force Delete Button -->
                                            <button onclick="forceDeleteUser({{ $user->id }})" 
                                                    class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive"
                                                    title="Permanently Delete">
                                                <i class="ki-filled ki-trash"></i>
                                            </button>
                                        @else
                                            <!-- Edit Button -->
                                            <a href="{{ route('users.edit', $user) }}" 
                                               class="kt-btn kt-btn-sm kt-btn-outline"
                                               title="Edit User">
                                                <i class="ki-filled ki-pencil"></i>
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <button onclick="deleteUser({{ $user->id }})" 
                                                    class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive"
                                                    title="Delete User">
                                                <i class="ki-filled ki-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="ki-filled ki-user text-3xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No users found</h3>
                                        <p class="text-gray-500 mb-6 max-w-md mx-auto">Get started by creating your first user account.</p>
                                        <a href="{{ route('users.create') }}" class="kt-btn kt-btn-primary">
                                            <i class="ki-filled ki-plus"></i>
                                            Add New User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            <div class="space-y-4 p-4">
                @forelse($users as $user)
                    <div class="kt-card border border-gray-200">
                        <div class="kt-card-content p-4">
                            <!-- User Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-white">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($user->trashed())
                                        <span class="kt-badge kt-badge-destructive">Deleted</span>
                                    @else
                                        <span class="kt-badge kt-badge-success">Active</span>
                                    @endif
                                </div>
                            </div>

                            <!-- User Details -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Role</div>
                                    <div>
                                        @if($user->hasRole('admin'))
                                            <span class="kt-badge kt-badge-primary">Admin</span>
                                        @elseif($user->hasRole('manager'))
                                            <span class="kt-badge kt-badge-info">Manager</span>
                                        @else
                                            <span class="kt-badge kt-badge-secondary">User</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Created</div>
                                    <div class="text-sm text-gray-900">{{ $user->created_at->format('M j, Y') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                @if($user->trashed())
                                    <!-- Restore Button -->
                                    <button onclick="restoreUser({{ $user->id }})" 
                                            class="kt-btn kt-btn-sm kt-btn-outline kt-btn-primary"
                                            title="Restore User">
                                        <i class="ki-filled ki-arrow-up"></i>
                                        <span class="ml-1">Restore</span>
                                    </button>
                                    
                                    <!-- Force Delete Button -->
                                    <button onclick="forceDeleteUser({{ $user->id }})" 
                                            class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive"
                                            title="Permanently Delete">
                                        <i class="ki-filled ki-trash"></i>
                                        <span class="ml-1">Delete</span>
                                    </button>
                                @else
                                    <!-- Edit Button -->
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="kt-btn kt-btn-sm kt-btn-outline"
                                       title="Edit User">
                                        <i class="ki-filled ki-pencil"></i>
                                        <span class="ml-1">Edit</span>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <button onclick="deleteUser({{ $user->id }})" 
                                            class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive"
                                            title="Delete User">
                                        <i class="ki-filled ki-trash"></i>
                                        <span class="ml-1">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="kt-card border border-gray-200">
                        <div class="kt-card-content p-8 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ki-filled ki-user text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No users found</h3>
                            <p class="text-gray-500 mb-6">Get started by creating your first user account.</p>
                            <a href="{{ route('users.create') }}" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-plus"></i>
                                Add New User
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div> 