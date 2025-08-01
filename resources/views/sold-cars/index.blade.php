@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <h2 class="text-lg font-bold">Sold Cars</h2>
    </div>
    <div class="kt-card-content p-4">
        @php
            $totalSales = $soldCars->total();
            $totalRevenue = $soldCars->sum('sale_price');
            $salesByUser = \App\Models\SoldCar::with('soldByUser')
                ->selectRaw('sold_by_user_id, COUNT(*) as total_sales, SUM(sale_price) as total_revenue')
                ->groupBy('sold_by_user_id')
                ->get();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="text-2xl font-bold text-blue-600">{{ $totalSales }}</div>
                <div class="text-sm text-blue-700">Total Cars Sold</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="text-2xl font-bold text-green-600">${{ number_format($totalRevenue, 0) }}</div>
                <div class="text-sm text-green-700">Total Revenue</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="text-2xl font-bold text-purple-600">{{ $salesByUser->count() }}</div>
                <div class="text-sm text-purple-700">Active Sellers</div>
            </div>
        </div>
        
        @if($salesByUser->count() > 0)
        <div class="mb-6">
            <h3 class="text-md font-semibold mb-3">Sales by User</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($salesByUser as $userSale)
                <div class="bg-gray-50 p-3 rounded border">
                    <div class="font-medium text-gray-900">{{ $userSale->soldByUser->name ?? 'Unknown' }}</div>
                    <div class="text-sm text-gray-600">{{ $userSale->total_sales }} cars • ${{ number_format($userSale->total_revenue, 0) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="kt-card-body">
        <!-- Filter Section -->
        <div class="mb-6">
            <form method="GET" action="{{ route('sold-cars.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="min-w-[200px]">
                    <label for="sold_by_user" class="block text-sm font-medium text-gray-700 mb-2">Sold By</label>
                    <select name="sold_by_user" id="sold_by_user" class="kt-select w-full">
                        <option value="">All Users</option>
                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}" {{ request('sold_by_user') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="kt-select w-full">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="check" {{ request('payment_method') === 'check' ? 'selected' : '' }}>Check</option>
                        <option value="separated" {{ request('payment_method') === 'separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-magnifier"></i>
                        Filter
                    </button>
                    <a href="{{ route('sold-cars.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-cross"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <table class="kt-table w-full">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Sale Price</th>
                    <th>Payment Method</th>
                    <th>Sold By</th>
                    <th>Sold At</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($soldCars as $soldCar)
                    <tr>
                        <td>{{ $soldCar->car->model ?? '-' }}</td>
                        <td>${{ number_format($soldCar->sale_price, 2) }}</td>
                        <td>{{ ucfirst($soldCar->payment_method) }}</td>
                        <td>{{ $soldCar->soldByUser->name ?? 'Unknown' }}</td>
                        <td>{{ $soldCar->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('sold-cars.show', $soldCar) }}" class="kt-btn kt-btn-sm kt-btn-outline">View Sale</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500">No sold cars found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $soldCars->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection 