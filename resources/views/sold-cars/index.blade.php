@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <h2 class="text-lg font-bold">Sold Cars</h2>
    </div>
    <div class="kt-card-body">
        <table class="kt-table w-full">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Sale Price</th>
                    <th>Payment Method</th>
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
                        <td>{{ $soldCar->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('cars.show', $soldCar->car) }}" class="kt-btn kt-btn-sm kt-btn-outline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500">No sold cars found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $soldCars->links() }}
        </div>
    </div>
</div>
@endsection 