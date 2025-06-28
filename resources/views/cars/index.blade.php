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
                            <input type="text" placeholder="Search cars..." class="kt-input w-full pl-10" />
                            <i
                                class="ki-filled ki-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Filters and Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Status Filter -->
                        <select class="kt-select kt-select-sm">
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
                        <select class="kt-select kt-select-sm">
                            <option value="">All Years</option>
                            @for ($year = date('Y'); $year >= 2015; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>

                        <!-- Add Car Button -->
                        <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-plus"></i>
                            Add Car
                        </a>
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

        <!-- Cars Grid View -->
        <div id="grid-view" class="view-container">
            @if ($cars->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($cars as $car)
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
                                                    <a href="{{ route('cars.show', $car) }}" class="kt-dropdown-menu-link">
                                                        <i class="ki-filled ki-search-list"></i>
                                                        View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('cars.edit', $car) }}" class="kt-dropdown-menu-link">
                                                        <i class="ki-filled ki-pencil"></i>
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('cars.destroy', $car) }}" method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this car?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="kt-dropdown-menu-link w-full text-left text-red-600">
                                                            <i class="ki-filled ki-trash"></i>
                                                            Remove
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                    @php
                                        $statusConfig = [
                                            'not_received' => ['class' => 'kt-badge-warning', 'text' => 'Not Received'],
                                            'paint' => ['class' => 'kt-badge-info', 'text' => 'Paint'],
                                            'upholstery' => ['class' => 'kt-badge-primary', 'text' => 'Upholstery'],
                                            'mechanic' => ['class' => 'kt-badge-warning', 'text' => 'Mechanic'],
                                            'electrical' => ['class' => 'kt-badge-warning', 'text' => 'Electrical'],
                                            'agency' => ['class' => 'kt-badge-info', 'text' => 'Agency'],
                                            'polish' => ['class' => 'kt-badge-primary', 'text' => 'Polish'],
                                            'ready' => ['class' => 'kt-badge-success', 'text' => 'Ready'],
                                        ];
                                        $status = $statusConfig[$car->status] ?? [
                                            'class' => 'kt-badge-secondary',
                                            'text' => ucfirst(str_replace('_', ' ', $car->status)),
                                        ];
                                    @endphp
                                    <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="kt-card-body p-4">
                                <!-- Car Image Placeholder -->
                                @if ($car->getFirstMedia('car_images'))
                                    <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                                        <img alt="Car Image" class="h-full w-full object-cover rounded"
                                            src="{{ $car->getFirstMedia('car_images')->getUrl() }}">
                                    </div>
                                @else
                                    <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                                        <i class="ki-filled ki-car text-4xl text-gray-400"></i>
                                    </div>
                                @endif

                                <!-- Car Details -->
                                <div class="space-y-3">
                                    <!-- Model and Year -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $car->model }}</h3>
                                        <p class="text-sm text-gray-500">{{ $car->manufacturing_year }} •
                                            {{ $car->engine_capacity }}</p>
                                    </div>

                                    <!-- Chassis and Plate -->
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-gear text-gray-400"></i>
                                            <span class="text-sm font-mono">{{ $car->chassis_number }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-tag text-gray-400"></i>
                                            <span class="text-sm">{{ $car->plate_number ?? '—' }}</span>
                                        </div>
                                    </div>

                                    <!-- Keys and Price -->
                                    <div class="flex items-center justify-between pt-2 border-t  border-gray-200">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-key text-gray-400"></i>
                                            <span class="text-sm">{{ $car->number_of_keys }} keys</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-green-600">
                                                ${{ number_format($car->expected_sale_price, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="kt-card-footer p-4 border-t border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="ki-filled ki-calendar text-gray-400"></i>
                                        <span class="text-gray-600">Purchased:
                                            {{ $car->purchase_date->format('M j, Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="ki-filled ki-shield-tick text-gray-400"></i>
                                        <span class="text-gray-600">Insurance:
                                            {{ $car->insurance_expiry_date->format('M j, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="kt-card">
                    <div class="kt-card-body">
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No cars found</h3>
                            <p class="text-gray-500 mb-6">Get started by adding your first car to the inventory.</p>
                            <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-plus"></i>
                                Add Your First Car
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Cars List View -->
        <div id="list-view" class="view-container hidden">
            @if ($cars->count() > 0)
                <div class="grid grid-cols-1 gap-5">
                    @foreach ($cars as $car)
                        <div class="kt-card">
                            <div
                                class="kt-card-content flex flex-col sm:flex-row items-center flex-wrap justify-between p-2 pe-5 gap-4.5">
                                <!-- Image -->
                                <div
                                    class="kt-card flex items-center justify-center bg-accent/50 h-[70px] w-[90px] shadow-none">
                                    @if (isset($car->image) && $car->image)
                                        <img alt="img" class="h-[70px] w-[90px] object-cover rounded"
                                            src="{{ asset('storage/' . $car->image) }}">
                                    @else
                                        <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                                    @endif
                                </div>
                                <!-- Details -->
                                <div class="flex flex-col gap-2 flex-1 min-w-0">
                                    <div class="flex items-center gap-2.5 -mt-1">
                                        <a class="hover:text-primary text-sm font-medium text-mono leading-5.5 truncate"
                                            href="{{ route('cars.show', $car) }}">
                                            {{ $car->model }}
                                        </a>
                                    </div>
                                    <div class="flex items-center flex-wrap gap-3">
                                        @php
                                            $statusConfig = [
                                                'not_received' => [
                                                    'class' => 'kt-badge-warning',
                                                    'text' => 'Not Received',
                                                ],
                                                'paint' => ['class' => 'kt-badge-info', 'text' => 'Paint'],
                                                'upholstery' => ['class' => 'kt-badge-primary', 'text' => 'Upholstery'],
                                                'mechanic' => ['class' => 'kt-badge-warning', 'text' => 'Mechanic'],
                                                'electrical' => ['class' => 'kt-badge-warning', 'text' => 'Electrical'],
                                                'agency' => ['class' => 'kt-badge-info', 'text' => 'Agency'],
                                                'polish' => ['class' => 'kt-badge-primary', 'text' => 'Polish'],
                                                'ready' => ['class' => 'kt-badge-success', 'text' => 'Ready'],
                                            ];
                                            $status = $statusConfig[$car->status] ?? [
                                                'class' => 'kt-badge-secondary',
                                                'text' => ucfirst(str_replace('_', ' ', $car->status)),
                                            ];
                                        @endphp
                                        <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>

                                        <div class="flex items-center flex-wrap gap-2 lg:gap-4">
                                            <span class="text-xs font-normal text-secondary-foreground uppercase">
                                                Chassis:
                                                <span class="text-xs font-medium text-foreground font-mono">
                                                    {{ $car->chassis_number }}
                                                </span>
                                            </span>
                                            @if ($car->plate_number)
                                                <span class="text-xs font-normal text-secondary-foreground">
                                                    Plate:
                                                    <span class="text-xs font-medium text-foreground">
                                                        {{ $car->plate_number }}
                                                    </span>
                                                </span>
                                            @endif
                                            <span class="text-xs font-normal text-secondary-foreground">
                                                Year:
                                                <span class="text-xs font-medium text-foreground">
                                                    {{ $car->manufacturing_year }}
                                                </span>
                                            </span>
                                            <span class="text-xs font-normal text-secondary-foreground">
                                                Engine:
                                                <span class="text-xs font-medium text-foreground">
                                                    {{ $car->engine_capacity }}
                                                </span>
                                            </span>
                                            <span class="text-xs font-normal text-secondary-foreground">
                                                Keys:
                                                <span class="text-xs font-medium text-foreground">
                                                    {{ $car->number_of_keys }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Price & Action -->
                                <div class="flex flex-col items-end gap-2 min-w-[120px]">
                                    <span class="text-sm font-medium text-mono">
                                        ${{ number_format($car->expected_sale_price, 2) }}
                                    </span>
                                    <!-- Actions Dropdown -->
                                    <div data-kt-dropdown="true" data-kt-dropdown-trigger="click"
                                        data-kt-dropdown-placement="left-start">
                                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-kt-dropdown-toggle="true">
                                            Actions
                                        </button>
                                        <div class="kt-dropdown-menu w-52" data-kt-dropdown-menu="true">
                                            <ul class="kt-dropdown-menu-sub">
                                                <li>
                                                    <a href="{{ route('cars.show', $car) }}"
                                                        class="kt-dropdown-menu-link">
                                                        <i class="ki-filled ki-search-list"></i>
                                                        View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('cars.edit', $car) }}"
                                                        class="kt-dropdown-menu-link">
                                                        <i class="ki-filled ki-pencil"></i>
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('cars.destroy', $car) }}" method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this car?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="kt-dropdown-menu-link w-full text-left text-red-600">
                                                            <i class="ki-filled ki-trash"></i>
                                                            Remove
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State for List View -->
                <div class="kt-card">
                    <div class="kt-card-body">
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No cars found</h3>
                            <p class="text-gray-500 mb-6">Get started by adding your first car to the inventory.</p>
                            <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-plus"></i>
                                Add Your First Car
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if ($cars->count() > 0)
            <div class="flex flex-col md:flex-row items-center justify-between mb-4">
                <div class="flex items-center justify-between mb-2 md:mb-0">
                    <div class="text-sm text-gray-600">
                        Showing {{ $cars->firstItem() ?? 0 }} to {{ $cars->lastItem() ?? 0 }} of {{ $cars->total() }} cars
                    </div>
                </div>
                <ol class="kt-pagination flex justify-center flex-wrap">
                    <li class="kt-pagination-item">
                        <a href="{{ $cars->url(1) }}" class="kt-btn kt-btn-icon kt-btn-ghost" aria-label="First Page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-first rtl:rotate-180"
                                aria-hidden="true">
                                <path d="m17 18-6-6 6-6"></path>
                                <path d="M7 6v12"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="kt-pagination-item">
                        <a href="{{ $cars->previousPageUrl() }}" class="kt-btn kt-btn-icon kt-btn-ghost"
                            aria-label="Previous Page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-left rtl:rotate-180" aria-hidden="true">
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                        </a>
                    </li>
                    @foreach ($cars->getUrlRange(1, $cars->lastPage()) as $page => $url)
                        <li class="kt-pagination-item">
                            <a href="{{ $url }}"
                                class="kt-btn kt-btn-icon kt-btn-ghost {{ $page == $cars->currentPage() ? 'active' : '' }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                    <li class="kt-pagination-item">
                        <a href="{{ $cars->nextPageUrl() }}" class="kt-btn kt-btn-icon kt-btn-ghost" aria-label="Next Page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-right rtl:rotate-180"
                                aria-hidden="true">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="kt-pagination-item">
                        <a href="{{ $cars->url($cars->lastPage()) }}" class="kt-btn kt-btn-icon kt-btn-ghost"
                            aria-label="Last Page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-last rtl:rotate-180" aria-hidden="true">
                                <path d="m7 18 6-6-6-6"></path>
                                <path d="M17 6v12"></path>
                            </svg>
                        </a>
                    </li>
                </ol>
            </div>
        @endif
    </div>

    <script>
        function switchView(viewType) {
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            const gridBtn = document.getElementById('grid-view-btn');
            const listBtn = document.getElementById('list-view-btn');

            if (viewType === 'grid') {
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
                localStorage.setItem('carViewType', 'grid');
            } else {
                listView.classList.remove('hidden');
                gridView.classList.add('hidden');
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
                localStorage.setItem('carViewType', 'list');
            }
        }

        // Load saved view preference on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('carViewType') || 'grid';
            switchView(savedView);
        });
    </script>
@endsection
