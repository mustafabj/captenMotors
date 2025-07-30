@extends('layouts.app')

@section('content')
    <div class="grid w-full space-y-5">
        <!-- Header with Search and Filters -->
        <div class="kt-card">
            <div class="kt-card-body">
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between p-4">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <div class="relative flex">
                            <input type="text" id="search-input" placeholder="Search by model or plate number..." class="kt-input w-full pl-10" />
                            <i
                                class="ki-filled ki-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Filters and Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Status Filter -->
                        <select id="status-filter" class="kt-select"
                        data-kt-select="true" data-kt-select-placeholder="Select Status">
                            <option value="">All Status</option>
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
                        <select id="year-filter" class="kt-select"
                        data-kt-select="true" data-kt-select-placeholder="Select Year">
                            <option value="">All Years</option>
                            @for ($year = date('Y'); $year >= 2015; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>

                        @if(auth()->user()->hasRole('admin'))
                        <!-- Add Car Button -->
                        <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-plus"></i>
                            Add Car
                        </a>

                        <!-- Insurance Check Button -->
                        <form action="{{ route('insurance.check-expiry') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="kt-btn kt-btn-warning" title="Check for insurance expiry notifications">
                                <i class="ki-filled ki-shield-tick"></i>
                                Check Insurance
                            </button>
                        </form>
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
