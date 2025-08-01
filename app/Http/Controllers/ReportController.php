<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\SoldCar;
use App\Models\StoreCapital;
use App\Models\CarEquipmentCost;
use App\Models\OtherCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
    }   


    public function profitLoss(Request $request)
    {
        // Get date range from request or default to current month
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Calculate totals
        $totalCapital = StoreCapital::currentTotal();
        
        // Total purchase cost of all cars (including sold and unsold)
        $totalPurchaseCost = Car::sum('purchase_price');
        
        // Total available capital including car investments
        $totalAvailableCapital = $totalCapital + $totalPurchaseCost;
        
        // Total sales revenue
        $totalSalesRevenue = SoldCar::sum('sale_price');
        
        // Total equipment costs (approved)
        $totalEquipmentCosts = CarEquipmentCost::where('status', 'approved')->sum('amount');
        
        // Total other costs
        $totalOtherCosts = OtherCost::sum('amount');
        
        // Calculate net profit/loss
        $totalCosts = $totalPurchaseCost + $totalEquipmentCosts + $totalOtherCosts;
        $netProfit = $totalSalesRevenue - $totalCosts;
        
        // Get sold cars for detailed breakdown
        $soldCars = SoldCar::with('car')
            ->when($startDate, function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                $query->where('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.profit-loss', compact(
            'totalCapital',
            'totalPurchaseCost',
            'totalAvailableCapital',
            'totalSalesRevenue',
            'totalEquipmentCosts',
            'totalOtherCosts',
            'totalCosts',
            'netProfit',
            'soldCars',
            'startDate',
            'endDate'
        ));
    }

    public function inventoryValuation()
    {
        // Get current inventory (cars not sold)
        $currentInventory = Car::where('status', '!=', 'sold')
            ->with(['options', 'equipmentCosts', 'otherCosts'])
            ->get();

        // Get capital information
        $totalCapital = StoreCapital::currentTotal();
        $totalCarInvestments = $currentInventory->sum('purchase_price');
        $totalAvailableCapital = $totalCapital + Car::sum('purchase_price'); // All cars including sold

        // Calculate total purchase value
        $totalPurchaseValue = $currentInventory->sum('purchase_price');
        
        // Calculate total expected sale value
        $totalExpectedSaleValue = $currentInventory->sum('expected_sale_price');
        
        // Calculate total equipment costs for current inventory
        $totalEquipmentCosts = $currentInventory->sum(function($car) {
            return $car->equipmentCosts->where('status', 'approved')->sum('amount');
        });
        
        // Calculate total other costs for current inventory
        $totalOtherCosts = $currentInventory->sum(function($car) {
            return $car->otherCosts->sum('amount');
        });
        
        // Total current value (purchase + costs)
        $totalCurrentValue = $totalPurchaseValue + $totalEquipmentCosts + $totalOtherCosts;
        
        // Potential profit if all cars sold at expected price
        $potentialProfit = $totalExpectedSaleValue - $totalCurrentValue;

        return view('reports.inventory-valuation', compact(
            'currentInventory',
            'totalCapital',
            'totalCarInvestments',
            'totalAvailableCapital',
            'totalPurchaseValue',
            'totalExpectedSaleValue',
            'totalEquipmentCosts',
            'totalOtherCosts',
            'totalCurrentValue',
            'potentialProfit'
        ));
    }

    public function equipmentCostSummary()
    {
        // Get equipment costs with car and user info
        $equipmentCosts = CarEquipmentCost::with(['car', 'user'])           ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals by status
        $totalPending = $equipmentCosts->where('status', 'pending')->sum('amount');       $totalApproved = $equipmentCosts->where('status', 'approved')->sum('amount');
        $totalRejected = $equipmentCosts->where('status', 'rejected')->sum('amount');       $totalTransferred = $equipmentCosts->where('status', 'transferred')->sum('amount');
        
        $totalAll = $equipmentCosts->sum('amount');

        // Group by car for summary
        $costsByCar = $equipmentCosts->groupBy('car_id');

        return view('reports.equipment-cost-summary', compact(
            'equipmentCosts',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'totalTransferred',
            'totalAll',
            'costsByCar'
        ));
    }

    public function allCars(Request $request)
    {
        // Get filters from request
        $status = $request->get('status');
        $year = $request->get('year');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Build query
        $query = Car::with([
            'options', 
            'inspection', 
            'statusHistories', 
            'equipmentCosts' => function($q) {
                $q->where('status', 'approved');
            }, 
            'otherCosts',
            'soldCar',
            'soldCar.soldByUser'
        ]);

        // Apply filters
        if ($status && $status !== 'all') {
            if ($status === 'not_ready') {
                $query->where('status', '!=', 'ready');
            } else {
                $query->where('status', $status);
            }
        }

        if ($year) {
            $query->where('manufacturing_year', $year);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', '%' . $search . '%')
                  ->orWhere('plate_number', 'like', '%' . $search . '%')
                  ->orWhere('vehicle_category', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $cars = $query->paginate(20)->withQueryString();

        // Calculate summary statistics
        $totalCars = Car::count();
        $totalSold = Car::where('status', 'sold')->count();
        $totalAvailable = $totalCars - $totalSold;
        $totalPurchaseValue = Car::sum('purchase_price');
        $totalExpectedValue = Car::sum('expected_sale_price');
        $totalEquipmentCosts = CarEquipmentCost::where('status', 'approved')->sum('amount');
        $totalOtherCosts = OtherCost::sum('amount');

        // Get status distribution
        $statusDistribution = Car::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get year distribution
        $yearDistribution = Car::selectRaw('manufacturing_year, COUNT(*) as count')
            ->groupBy('manufacturing_year')
            ->orderBy('manufacturing_year', 'desc')
            ->pluck('count', 'manufacturing_year')
            ->toArray();

        return view('reports.all-cars', compact(
            'cars',
            'totalCars',
            'totalSold',
            'totalAvailable',
            'totalPurchaseValue',
            'totalExpectedValue',
            'totalEquipmentCosts',
            'totalOtherCosts',
            'statusDistribution',
            'yearDistribution',
            'status',
            'year',
            'search',
            'sortBy',
            'sortOrder'
        ));
    }
}
 