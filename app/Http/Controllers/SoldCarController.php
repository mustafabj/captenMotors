<?php

namespace App\Http\Controllers;

use App\Models\SoldCar;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoldCarController extends Controller
{
    public function index(Request $request)
    {
        $query = SoldCar::with(['car', 'soldByUser'])
            ->when($request->sold_by_user, function($q) use ($request) {
                $q->where('sold_by_user_id', $request->sold_by_user);
            })
            ->when($request->payment_method, function($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        
        $soldCars = $query->latest()->paginate(15);
        return view('sold-cars.index', compact('soldCars'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'sold_by_user_id' => 'required|exists:users,id',
            'sale_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,check,separated',
            'paid_amount' => 'nullable|numeric|min:0',
            'remaining_amount' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);

        // Check if car is already sold
        $existingSale = SoldCar::where('car_id', $validated['car_id'])->first();
        if ($existingSale) {
            return back()->withErrors(['car_id' => 'This car has already been sold.'])->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('sold_car_attachments', 'public');
        }

        $soldCar = SoldCar::create([
            'car_id' => $validated['car_id'],
            'sold_by_user_id' => $validated['sold_by_user_id'],
            'sale_price' => $validated['sale_price'],
            'payment_method' => $validated['payment_method'],
            'paid_amount' => $validated['paid_amount'] ?? null,
            'remaining_amount' => $validated['remaining_amount'] ?? null,
            'attachment' => $attachmentPath,
        ]);

        // Mark car as sold
        $car = Car::findOrFail($validated['car_id']);
        $car->status = 'sold';
        $car->save();

        return redirect()->route('cars.show', $car)->with('success', 'Car marked as sold.');
    }

    public function show(SoldCar $soldCar)
    {
        $soldCar->load(['car', 'soldByUser']);
        return view('sold-cars.show', compact('soldCar'));
    }
} 