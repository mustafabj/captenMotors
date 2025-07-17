<?php

namespace App\Http\Controllers;

use App\Models\SoldCar;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoldCarController extends Controller
{
    public function index()
    {
        $soldCars = SoldCar::with('car')->latest()->paginate(15);
        return view('sold-cars.index', compact('soldCars'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
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
} 