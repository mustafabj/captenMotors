<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of advertisements
     */
    public function index(Request $request)
    {
        $query = Advertisement::with(['car', 'user'])
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->user_id, function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->search, function($q) use ($request) {
                $q->whereHas('car', function($carQuery) use ($request) {
                    $carQuery->where('model', 'like', '%' . $request->search . '%')
                            ->orWhere('plate_number', 'like', '%' . $request->search . '%');
                });
            });

        // If user is not admin, only show their own advertisements
        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $advertisements = $query->latest()->paginate(15);
        $users = User::all(); // For admin filter

        return view('advertisements.index', compact('advertisements', 'users'));
    }

    /**
     * Show the form for creating a new advertisement
     */
    public function create()
    {
        // Get available cars (not sold and not already advertised)
        $availableCars = Car::where('status', '!=', 'sold')
            ->whereDoesntHave('advertisements', function($query) {
                $query->where('status', 'active');
            })
            ->orderBy('model')
            ->get();

        return view('advertisements.create', compact('availableCars'));
    }

    /**
     * Store a newly created advertisement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'expiration_date' => 'required|date|after:today',
            'offer_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if car is already advertised
        $existingAd = Advertisement::where('car_id', $validated['car_id'])
            ->where('status', 'active')
            ->first();

        if ($existingAd) {
            return back()->withErrors(['car_id' => 'This car is already advertised.'])->withInput();
        }

        // Check if car is sold
        $car = Car::find($validated['car_id']);
        if ($car->isSold()) {
            return back()->withErrors(['car_id' => 'Cannot advertise a sold car.'])->withInput();
        }

        $advertisement = Advertisement::create([
            'car_id' => $validated['car_id'],
            'user_id' => Auth::id(),
            'expiration_date' => $validated['expiration_date'],
            'offer_price' => $validated['offer_price'],
            'sale_price' => $validated['sale_price'],
            'description' => $validated['description'],
            'status' => 'active'
        ]);

        return redirect()->route('advertisements.index')
            ->with('success', 'Advertisement created successfully!');
    }

    /**
     * Display the specified advertisement
     */
    public function show(Advertisement $advertisement)
    {
        // Check if user can view this advertisement
        if (!Auth::user()->isAdmin() && $advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $advertisement->load(['car', 'user']);
        return view('advertisements.show', compact('advertisement'));
    }

    /**
     * Show the form for editing the specified advertisement
     */
    public function edit(Advertisement $advertisement)
    {
        // Check if user can edit this advertisement
        if (!Auth::user()->isAdmin() && $advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if advertisement can be edited
        if ($advertisement->status !== 'active') {
            return redirect()->route('advertisements.index')
                ->with('error', 'Only active advertisements can be edited.');
        }

        $availableCars = Car::where('status', '!=', 'sold')
            ->where(function($query) use ($advertisement) {
                $query->whereDoesntHave('advertisements', function($adQuery) use ($advertisement) {
                    $adQuery->where('status', 'active')->where('id', '!=', $advertisement->id);
                })
                ->orWhere('id', $advertisement->car_id);
            })
            ->orderBy('model')
            ->get();

        return view('advertisements.edit', compact('advertisement', 'availableCars'));
    }

    /**
     * Update the specified advertisement
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        // Check if user can update this advertisement
        if (!Auth::user()->isAdmin() && $advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'expiration_date' => 'required|date|after:today',
            'offer_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if car is already advertised by another advertisement
        $existingAd = Advertisement::where('car_id', $validated['car_id'])
            ->where('status', 'active')
            ->where('id', '!=', $advertisement->id)
            ->first();

        if ($existingAd) {
            return back()->withErrors(['car_id' => 'This car is already advertised.'])->withInput();
        }

        $advertisement->update($validated);

        return redirect()->route('advertisements.index')
            ->with('success', 'Advertisement updated successfully!');
    }

    /**
     * Remove the specified advertisement
     */
    public function destroy(Advertisement $advertisement)
    {
        // Check if user can delete this advertisement
        if (!Auth::user()->isAdmin() && $advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $advertisement->delete();

        return redirect()->route('advertisements.index')
            ->with('success', 'Advertisement deleted successfully!');
    }

    /**
     * Mark advertisement as sold
     */
    public function markAsSold(Advertisement $advertisement)
    {
        // Check if user can modify this advertisement
        if (!Auth::user()->isAdmin() && $advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $advertisement->markAsSold();

        return redirect()->route('advertisements.index')
            ->with('success', 'Advertisement marked as sold!');
    }

    /**
     * Mark advertisement as cancelled
     */
    public function markAsCancelled(Advertisement $advertisement)
    {
        // Check if user can modify this advertisement
        if (!Auth::user()->isAdmin() && $advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $advertisement->markAsCancelled();

        return redirect()->route('advertisements.index')
            ->with('success', 'Advertisement cancelled successfully!');
    }

    /**
     * Check and update expired advertisements
     */
    public function checkExpired()
    {
        $expiredAds = Advertisement::active()
            ->where('expiration_date', '<', now()->toDateString())
            ->get();

        foreach ($expiredAds as $ad) {
            $ad->markAsExpired();
        }

        return redirect()->route('advertisements.index')
            ->with('success', count($expiredAds) . ' advertisements marked as expired.');
    }
}
