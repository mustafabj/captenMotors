<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BulkDealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EquipmentCostNotificationController;
use App\Http\Controllers\OtherCostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\SoldCarController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('index');
})->middleware(['auth', 'verified'])->name('index');


Route::middleware('auth')->group(function () {
    // Profile routes (all users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Car routes (limited for regular users)
    Route::get('cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('cars/search', [CarController::class, 'search'])->name('cars.search');
    Route::post('cars/{id}/equipment-costs', [CarController::class, 'addEquipmentCost'])->name('cars.add-equipment-cost');
    
    // Image editing routes (available to all authenticated users)
    Route::post('cars/{id}/update-images', [CarController::class, 'updateImages'])->name('cars.update-images');
    Route::post('cars/{id}/delete-image', [CarController::class, 'deleteImage'])->name('cars.delete-image');

    // Notification routes (all users)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Equipment Cost Notification routes (limited)
    Route::post('equipment-cost-notifications/{id}/mark-read', [EquipmentCostNotificationController::class, 'markAsRead'])->name('equipment-cost-notifications.mark-read');
    Route::post('equipment-cost-notifications/mark-all-read', [EquipmentCostNotificationController::class, 'markAllAsRead'])->name('equipment-cost-notifications.mark-all-read');
    Route::get('equipment-cost-notifications/unread-count', [EquipmentCostNotificationController::class, 'unreadCount'])->name('equipment-cost-notifications.unread-count');
    Route::delete('equipment-cost-notifications/{id}', [EquipmentCostNotificationController::class, 'destroy'])->name('equipment-cost-notifications.destroy');

    // Other Costs routes (view only for regular users)
    Route::get('other-costs', [OtherCostController::class, 'index'])->name('other-costs.index');
    Route::get('other-costs/{otherCost}', [OtherCostController::class, 'show'])->name('other-costs.show');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Car management (create, edit, delete, status updates)
    Route::get('cars/create', [CarController::class, 'create'])->name('cars.create');
    Route::post('cars', [CarController::class, 'store'])->name('cars.store');
    Route::get('cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
    Route::put('cars/{car}', [CarController::class, 'update'])->name('cars.update');
    Route::delete('cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
    Route::post('cars/validate-step', [CarController::class, 'validateStep'])->name('cars.validate-step');
    Route::post('cars/{id}/update-status', [CarController::class, 'updateStatus'])->name('cars.update-status');
    Route::post('cars/{id}/update-inline', [CarController::class, 'updateInline'])->name('cars.update-inline');
    Route::post('cars/{id}/update-options', [CarController::class, 'updateOptions'])->name('cars.update-options');
    Route::post('cars/{id}/update-inspection', [CarController::class, 'updateInspection'])->name('cars.update-inspection');
    Route::get('cars/{car}/inspection-report', [CarController::class, 'inspectionReport'])->name('cars.inspection-report');
    Route::post('cars/{id}/update-financial', [CarController::class, 'updateFinancial'])->name('cars.update-financial');

    // Bulk Deal routes (admin only)
    Route::resource('bulk-deals', BulkDealController::class);

    // User management (admin only)
    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::post('users/{id}/update-inline', [UserController::class, 'updateInline'])->name('users.update-inline');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::resource('users', UserController::class);

    // Equipment Cost Notifications (admin approval interface)
    Route::get('equipment-cost-notifications', [EquipmentCostNotificationController::class, 'index'])->name('equipment-cost-notifications.index');
    Route::get('equipment-cost-notifications/{id}', [EquipmentCostNotificationController::class, 'show'])->name('equipment-cost-notifications.show');
    Route::post('equipment-cost-notifications/equipment-cost/{costId}/approve', [EquipmentCostNotificationController::class, 'approveEquipmentCost'])->name('equipment-cost-notifications.approve-equipment-cost');
    Route::post('equipment-cost-notifications/equipment-cost/{costId}/reject', [EquipmentCostNotificationController::class, 'rejectEquipmentCost'])->name('equipment-cost-notifications.reject-equipment-cost');
    Route::post('equipment-cost-notifications/equipment-cost/{costId}/transfer', [EquipmentCostNotificationController::class, 'transferEquipmentCost'])->name('equipment-cost-notifications.transfer-equipment-cost');

    // Other Costs creation and management (admin only)
    Route::get('other-costs/create', [OtherCostController::class, 'create'])->name('other-costs.create');
    Route::post('other-costs', [OtherCostController::class, 'store'])->name('other-costs.store');
    Route::get('other-costs/{otherCost}/edit', [OtherCostController::class, 'edit'])->name('other-costs.edit');
    Route::put('other-costs/{otherCost}', [OtherCostController::class, 'update'])->name('other-costs.update');
    Route::delete('other-costs/{otherCost}', [OtherCostController::class, 'destroy'])->name('other-costs.destroy');
    Route::post('other-costs/transfer-from-equipment-cost/{equipmentCostId}', [OtherCostController::class, 'transferFromEquipmentCost'])->name('other-costs.transfer-from-equipment-cost');

    // Sold Cars routes (admin only)
    Route::resource('sold-cars', SoldCarController::class)->only(['index', 'store']);
    Route::post('cars/{car}/sell', [SoldCarController::class, 'store'])->name('cars.sell');

    // Store Capital routes (admin only)
    Route::get('store-capital', [\App\Http\Controllers\StoreCapitalController::class, 'index'])->name('store-capital.index');
    Route::get('store-capital/create', [\App\Http\Controllers\StoreCapitalController::class, 'create'])->name('store-capital.create');
    Route::post('store-capital', [\App\Http\Controllers\StoreCapitalController::class, 'store'])->name('store-capital.store');

    // Reports section (admin only)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/inventory-valuation', [ReportController::class, 'inventoryValuation'])->name('inventory-valuation');
        Route::get('/equipment-cost-summary', [ReportController::class, 'equipmentCostSummary'])->name('equipment-cost-summary');
    });
});

// Car show route (must be last to avoid conflicts with specific routes)
Route::middleware('auth')->get('cars/{car}', [CarController::class, 'show'])->name('cars.show');

require __DIR__.'/auth.php';
