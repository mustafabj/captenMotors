@extends('layouts.app')

@section('content')
<div class="grid w-full space-y-5">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Advertisement Details</h1>
            <p class="text-sm text-gray-500">View advertisement information</p>
        </div>
        <div class="flex gap-2">
            @if($advertisement->status === 'active' && (auth()->user()->isAdmin() || $advertisement->user_id === auth()->id()))
                <a href="{{ route('advertisements.edit', $advertisement) }}" class="kt-btn kt-btn-warning">
                    <i class="ki-filled ki-pencil"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route('advertisements.index') }}" class="kt-btn kt-btn-outline">
                <i class="ki-filled ki-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Advertisement Information</h3>
                </div>
                <div class="kt-card-content p-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h5 class="text-lg font-semibold text-gray-900 mb-4">Car Information</h5>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Model:</span>
                                    <span class="text-gray-900">{{ $advertisement->car->model }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Plate Number:</span>
                                    <span class="text-gray-900">{{ $advertisement->car->plate_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Manufacturing Year:</span>
                                    <span class="text-gray-900">{{ $advertisement->car->manufacturing_year }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Color:</span>
                                    <span class="text-gray-900">{{ $advertisement->car->color ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Odometer:</span>
                                    <span class="text-gray-900">{{ number_format($advertisement->car->odometer) ?? 'N/A' }} km</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Engine Capacity:</span>
                                    <span class="text-gray-900">{{ $advertisement->car->engine_capacity ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="text-lg font-semibold text-gray-900 mb-4">Advertisement Information</h5>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Posted By:</span>
                                    <span class="text-gray-900">{{ $advertisement->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <span>
                                        @switch($advertisement->status)
                                            @case('active')
                                                <span class="kt-badge kt-badge-success">Active</span>
                                                @break
                                            @case('expired')
                                                <span class="kt-badge kt-badge-warning">Expired</span>
                                                @break
                                            @case('sold')
                                                <span class="kt-badge kt-badge-info">Sold</span>
                                                @break
                                            @case('cancelled')
                                                <span class="kt-badge kt-badge-secondary">Cancelled</span>
                                                @break
                                        @endswitch
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <span class="text-gray-900">{{ $advertisement->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Last Updated:</span>
                                    <span class="text-gray-900">{{ $advertisement->updated_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Pricing Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="kt-card bg-gray-50">
                            <div class="kt-card-content p-4 text-center">
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Offer Price</h6>
                                <h4 class="text-xl font-bold text-blue-600">${{ number_format($advertisement->offer_price, 2) }}</h4>
                                <small class="text-gray-500">Purchase Price</small>
                            </div>
                        </div>

                        <div class="kt-card bg-gray-50">
                            <div class="kt-card-content p-4 text-center">
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Sale Price</h6>
                                <h4 class="text-xl font-bold text-green-600">${{ number_format($advertisement->sale_price, 2) }}</h4>
                                <small class="text-gray-500">Selling Price</small>
                            </div>
                        </div>

                        <div class="kt-card bg-gray-50">
                            <div class="kt-card-content p-4 text-center">
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Profit</h6>
                                <h4 class="text-xl font-bold text-purple-600">${{ number_format($advertisement->getProfit(), 2) }}</h4>
                                <small class="text-gray-500">{{ number_format($advertisement->getProfitPercentage(), 1) }}%</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="kt-card">
                            <div class="kt-card-content p-4">
                                <h6 class="text-lg font-semibold text-gray-900 mb-3">Expiration Information</h6>
                                <p class="mb-2"><strong>Expiration Date:</strong> {{ $advertisement->expiration_date->format('M d, Y') }}</p>
                                @if($advertisement->isExpired())
                                    <p class="text-red-600 flex items-center gap-2">
                                        <i class="ki-filled ki-exclamation-triangle"></i> 
                                        Expired {{ number_format($advertisement->getDaysUntilExpiration()) }} days ago
                                    </p>
                                @elseif($advertisement->status === 'active')
                                    <p class="text-blue-600 flex items-center gap-2">
                                        <i class="ki-filled ki-clock"></i> 
                                        {{ number_format($advertisement->getDaysUntilExpiration()) }} days remaining
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="kt-card">
                            <div class="kt-card-content p-4">
                                <h6 class="text-lg font-semibold text-gray-900 mb-3">Actions</h6>
                                @if($advertisement->status === 'active' && (auth()->user()->isAdmin() || $advertisement->user_id === auth()->id()))
                                    <div class="space-y-2">
                                        <form method="POST" action="{{ route('advertisements.mark-as-sold', $advertisement) }}" class="w-full">
                                            @csrf
                                            <button type="submit" class="kt-btn kt-btn-success w-full" onclick="return confirm('Mark as sold?')">
                                                <i class="ki-filled ki-check"></i> Mark as Sold
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('advertisements.mark-as-cancelled', $advertisement) }}" class="w-full">
                                            @csrf
                                            <button type="submit" class="kt-btn kt-btn-secondary w-full" onclick="return confirm('Cancel advertisement?')">
                                                <i class="ki-filled ki-cross"></i> Cancel Advertisement
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('advertisements.destroy', $advertisement) }}" class="w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="kt-btn kt-btn-danger w-full" onclick="return confirm('Delete advertisement?')">
                                                <i class="ki-filled ki-trash"></i> Delete Advertisement
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-gray-500">No actions available for this advertisement.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($advertisement->description)
                        <div class="mt-6">
                            <div class="kt-card">
                                <div class="kt-card-content p-4">
                                    <h6 class="text-lg font-semibold text-gray-900 mb-3">Description</h6>
                                    <p class="text-gray-700">{{ $advertisement->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Car Images -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h5 class="text-lg font-semibold text-gray-900">Car Images</h5>
                </div>
                <div class="kt-card-content p-4">
                    @if($advertisement->car->getMedia('car_images')->count() > 0)
                        <div id="carImagesCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($advertisement->car->getMedia('car_images') as $index => $media)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $media->getUrl() }}" class="d-block w-100 rounded" alt="Car Image">
                                    </div>
                                @endforeach
                            </div>
                            @if($advertisement->car->getMedia('car_images')->count() > 1)
                                <a class="carousel-control-prev" href="#carImagesCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carImagesCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-center">No images available for this car.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="kt-card mt-4">
                <div class="kt-card-header">
                    <h5 class="text-lg font-semibold text-gray-900">Quick Stats</h5>
                </div>
                <div class="kt-card-content p-4">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <h6 class="text-sm font-medium text-gray-600">Profit Margin</h6>
                            <h4 class="text-xl font-bold text-green-600">{{ number_format($advertisement->getProfitPercentage(), 1) }}%</h4>
                        </div>
                        <div>
                            <h6 class="text-sm font-medium text-gray-600">Days Left</h6>
                            <h4 class="text-xl font-bold text-blue-600">{{ number_format($advertisement->getDaysUntilExpiration()) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 