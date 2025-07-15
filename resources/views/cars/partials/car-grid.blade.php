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
                                    <i class="ki-filled ki-tag text-gray-400"></i>
                                    <span class="text-sm">{{ $car->plate_number ?? '—' }}</span>
                                </div>
                            </div>

                            <!-- Keys and Price -->
                            <div class="flex items-center justify-between pt-2 border-t  border-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-key text-gray-400"></i>
                                    <span class="text-sm">{{ $car->number_of_keys ?? 0 }} keys</span>
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
                                    {{ $car->insurance_expiry_date ? $car->insurance_expiry_date->format('M j, Y') : 'Not set' }}</span>
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
                    <p class="text-gray-500 mb-6">Try adjusting your search terms or filters.</p>
                    <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add New Car
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
                            @if ($car->getFirstMedia('car_images'))
                                <img alt="img" class="h-[70px] w-[90px] object-cover rounded"
                                    src="{{ $car->getFirstMedia('car_images')->getUrl() }}">
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
                                        Keys:
                                        <span class="text-xs font-medium text-foreground">
                                            {{ $car->number_of_keys ?? 0 }}
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
                    <p class="text-gray-500 mb-6">Try adjusting your search terms or filters.</p>
                    <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add New Car
                    </a>
                </div>
            </div>
        </div>
    @endif
</div> 