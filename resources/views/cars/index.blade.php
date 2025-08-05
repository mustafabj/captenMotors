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
                            <input type="text" id="search-input" placeholder="Search by model or plate number..." class="kt-input w-full" />
                            <i
                                class="ki-filled ki-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Filters and Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Status Filter -->
                        <select id="status-filter" class="kt-select min-w-[150px]"
                        data-kt-select="true" data-kt-select-placeholder="Select Status">
                            <option value="">All Status</option>
                            <option value="not_ready">Not Ready</option>
                            <option value="not_received">Not Received</option>
                            <option value="paint">Paint</option>
                            <option value="upholstery">Upholstery</option>
                            <option value="mechanic">Mechanic</option>
                            <option value="electrical">Electrical</option>
                            <option value="agency">Agency</option>
                            <option value="polish">Polish</option>
                            <option value="ready">Ready</option>
                        </select>

                        <!-- Year Filter -->
                        <select id="year-filter" class="kt-select min-w-[100px]"
                        data-kt-select="true" data-kt-select-placeholder="Select Year">
                            <option value="">All Years</option>
                            @for ($year = date('Y'); $year >= 2015; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>

                        <!-- Show Cars Dropdown -->
                        <select id="per-page-filter" class="kt-select min-w-[100px]"
                        data-kt-select="true" data-kt-select-placeholder="Show cars">
                            <option value="6" {{ request('per_page') == 6 ? 'selected' : '' }}>Show 6</option>
                            <option value="12" {{ request('per_page') == 12 || !request('per_page') ? 'selected' : '' }}>Show 12</option>
                            <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>Show 24</option>
                            <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>Show 48</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>Show 100</option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>Show 500</option>
                        </select>

                        @if(auth()->user()->hasRole('admin'))
                        <!-- Add Car Button -->
                        <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-plus"></i>
                            Add Car
                        </a>

                        <!-- Insurance Check Button -->
                        <!-- <form action="{{ route('insurance.check-expiry') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="kt-btn kt-btn-warning" title="Check for insurance expiry notifications">
                                <i class="ki-filled ki-shield-tick"></i>
                                Check Insurance
                            </button>
                        </form> -->
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Showing {{ $cars->firstItem() ?? 0 }} to {{ $cars->lastItem() ?? 0 }} of {{ $cars->total() }} cars
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">View:</span>
                <button id="grid-view-btn" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline active"
                    onclick="switchView('grid')">
                    <i class="ki-filled ki-category"></i>
                </button>
                <button id="list-view-btn" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline" onclick="switchView('list')">
                    <i class="ki-filled ki-row-horizontal"></i>
                </button>
            </div>
        </div>

        <!-- Cars Display Container -->
        <div id="cars-container" data-search-url="{{ route('cars.search') }}">
            @include('cars.partials.car-grid', ['cars' => $cars])
        </div>

        <!-- Pagination Container -->
        <div id="pagination-container">
            @include('cars.partials.pagination', ['cars' => $cars])
        </div>
    </div>

@push('scripts')
<script src="{{ asset('js/pages/cars-index.js') }}"></script>
@endpush
@endsection
