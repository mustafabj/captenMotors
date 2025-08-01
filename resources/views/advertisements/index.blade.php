@extends('layouts.app')

@section('content')
<div class="grid w-full space-y-5">
    <!-- Header with Search and Filters -->
    <div class="kt-card">
        <div class="kt-card-body">
            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between p-4">
                <!-- Search Bar -->
                <div class="flex-1">
                    <div class="relative flex">
                        <input type="text" id="search-input" placeholder="Search by car model or plate number..." class="kt-input w-full" />
                        <i class="ki-filled ki-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filters and Actions -->
                <div class="flex items-center gap-3">
                    <!-- Status Filter -->
                    <select id="status-filter" class="kt-select min-w-[150px]" data-kt-select="true" data-kt-select-placeholder="Select Status">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="sold">Sold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    @if(auth()->user()->isAdmin())
                    <!-- User Filter -->
                    <select id="user-filter" class="kt-select min-w-[150px]" data-kt-select="true" data-kt-select-placeholder="Select User">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    <!-- Check Expired Button -->
                    <form action="{{ route('advertisements.check-expired') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="kt-btn kt-btn-warning" title="Check for expired advertisements">
                            <i class="ki-filled ki-clock"></i>
                            Check Expired
                        </button>
                    </form>
                    @endif

                    <!-- Create Advertisement Button -->
                    <a href="{{ route('advertisements.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Create Advertisement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Showing {{ $advertisements->firstItem() ?? 0 }} to {{ $advertisements->lastItem() ?? 0 }} of {{ $advertisements->total() }} advertisements
        </div>
    </div>

    <!-- Advertisements Grid -->
    <div id="advertisements-container">
        @if ($advertisements->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($advertisements as $advertisement)
                    <div class="kt-card group hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <!-- Card Header with Status -->
                        <div class="kt-card-header p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between w-full">
                                <!-- Actions Dropdown -->
                                <div data-kt-dropdown="true" data-kt-dropdown-trigger="click">
                                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline"
                                        data-kt-dropdown-toggle="true">
                                        <i class="ki-filled ki-dots-square"></i>
                                    </button>
                                    <div class="kt-dropdown-menu w-52" data-kt-dropdown-menu="true">
                                        <ul class="kt-dropdown-menu-sub">
                                            <li>
                                                <a href="{{ route('advertisements.show', $advertisement) }}" class="kt-dropdown-menu-link">
                                                    <i class="ki-filled ki-search-list"></i>
                                                    View
                                                </a>
                                            </li>
                                            @if($advertisement->status === 'active' && (auth()->user()->isAdmin() || $advertisement->user_id === auth()->id()))
                                            <li>
                                                <a href="{{ route('advertisements.edit', $advertisement) }}" class="kt-dropdown-menu-link">
                                                    <i class="ki-filled ki-pencil"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('advertisements.mark-as-sold', $advertisement) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="kt-dropdown-menu-link w-full text-left" onclick="return confirm('Mark as sold?')">
                                                        <i class="ki-filled ki-check"></i>
                                                        Mark as Sold
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('advertisements.mark-as-cancelled', $advertisement) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="kt-dropdown-menu-link w-full text-left" onclick="return confirm('Cancel advertisement?')">
                                                        <i class="ki-filled ki-cross"></i>
                                                        Cancel
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('advertisements.destroy', $advertisement) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="kt-dropdown-menu-link w-full text-left text-red-600" onclick="return confirm('Delete advertisement?')">
                                                        <i class="ki-filled ki-trash"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                @php
                                    $statusConfig = [
                                        'active' => ['class' => 'kt-badge-success', 'text' => 'Active'],
                                        'expired' => ['class' => 'kt-badge-warning', 'text' => 'Expired'],
                                        'sold' => ['class' => 'kt-badge-info', 'text' => 'Sold'],
                                        'cancelled' => ['class' => 'kt-badge-secondary', 'text' => 'Cancelled'],
                                    ];
                                    $status = $statusConfig[$advertisement->status] ?? [
                                        'class' => 'kt-badge-secondary',
                                        'text' => ucfirst($advertisement->status),
                                    ];
                                @endphp
                                <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="kt-card-body p-4">
                            <!-- Car Image Placeholder -->
                            @if ($advertisement->car->getFirstMedia('car_images'))
                                <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                                    <img alt="Car Image" class="h-full w-full object-cover rounded"
                                        src="{{ $advertisement->car->getFirstMedia('car_images')->getUrl() }}">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                                    <i class="ki-filled ki-car text-4xl text-gray-400"></i>
                                </div>
                            @endif

                            <!-- Advertisement Details -->
                            <div class="space-y-3">
                                <!-- Car Model and Plate -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $advertisement->car->model }}</h3>
                                    <p class="text-sm text-gray-500">{{ $advertisement->car->plate_number }} • {{ $advertisement->car->manufacturing_year }}</p>
                                </div>

                                <!-- Posted By -->
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-user text-gray-400"></i>
                                    <span class="text-sm text-gray-600">{{ $advertisement->user->name }}</span>
                                </div>

                                <!-- Pricing Information -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Offer Price:</span>
                                        <span class="text-sm font-medium">${{ number_format($advertisement->offer_price, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Sale Price:</span>
                                        <span class="text-sm font-medium text-green-600">${{ number_format($advertisement->sale_price, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Profit:</span>
                                        <span class="text-sm font-medium text-blue-600">
                                            ${{ number_format($advertisement->getProfit(), 2) }}
                                            <span class="text-xs">({{ number_format($advertisement->getProfitPercentage(), 1) }}%)</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Expiration Information -->
                                <div class="pt-2 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Expires:</span>
                                        <span class="text-sm font-medium">{{ $advertisement->expiration_date->format('M d, Y') }}</span>
                                    </div>
                                    @if($advertisement->isExpired())
                                        <div class="text-xs text-red-600 mt-1">
                                            <i class="ki-filled ki-exclamation-triangle"></i>
                                            Expired {{ number_format($advertisement->getDaysUntilExpiration()) }} days ago
                                        </div>
                                    @elseif($advertisement->status === 'active')
                                        <div class="text-xs text-blue-600 mt-1">
                                            <i class="ki-filled ki-clock"></i>
                                            {{ number_format($advertisement->getDaysUntilExpiration()) }} days left
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="kt-card">
                <div class="kt-card-body text-center py-12">
                    <i class="ki-filled ki-document text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No advertisements found</h3>
                    <p class="text-gray-600 mb-4">Create your first advertisement to get started.</p>
                    <a href="{{ route('advertisements.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Create Advertisement
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($advertisements->hasPages())
        <div class="flex justify-center">
            {{ $advertisements->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const userFilter = document.getElementById('user-filter');

    function applyFilters() {
        const search = searchInput.value;
        const status = statusFilter.value;
        const userId = userFilter ? userFilter.value : '';

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (userId) params.append('user_id', userId);

        window.location.href = '{{ route("advertisements.index") }}?' + params.toString();
    }

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    statusFilter.addEventListener('change', applyFilters);
    if (userFilter) {
        userFilter.addEventListener('change', applyFilters);
    }

    // Set current values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('search')) searchInput.value = urlParams.get('search');
    if (urlParams.get('status')) statusFilter.value = urlParams.get('status');
    if (userFilter && urlParams.get('user_id')) userFilter.value = urlParams.get('user_id');
});
</script>
@endpush
@endsection 