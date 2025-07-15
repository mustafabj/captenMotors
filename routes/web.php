<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BulkDealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    

    
    // Car routes with slug-based routing
    Route::get('cars/search', [CarController::class, 'search'])->name('cars.search');
    Route::post('cars/validate-step', [CarController::class, 'validateStep'])->name('cars.validate-step');
    Route::post('cars/{id}/equipment-costs', [CarController::class, 'addEquipmentCost'])->name('cars.add-equipment-cost');
    Route::post('cars/{id}/update-status', [CarController::class, 'updateStatus'])->name('cars.update-status');
    Route::post('cars/{id}/update-inline', [CarController::class, 'updateInline'])->name('cars.update-inline');
    Route::post('cars/{id}/update-options', [CarController::class, 'updateOptions'])->name('cars.update-options');
    Route::post('cars/{id}/update-inspection', [CarController::class, 'updateInspection'])->name('cars.update-inspection');
    Route::post('cars/{id}/update-financial', [CarController::class, 'updateFinancial'])->name('cars.update-financial');
    Route::post('cars/{id}/update-images', [CarController::class, 'updateImages'])->name('cars.update-images');
    Route::delete('cars/{id}/images', [CarController::class, 'deleteImage'])->name('cars.delete-image');
    Route::resource('cars', CarController::class)->parameters(['cars' => 'car']);

    // Bulk Deal routes
    Route::resource('bulk-deals', BulkDealController::class);

    // User routes
    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::post('users/{id}/update-inline', [UserController::class, 'updateInline'])->name('users.update-inline');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::resource('users', UserController::class);

    // Notification routes
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('notifications/equipment-cost/{costId}/approve', [NotificationController::class, 'approveEquipmentCost'])->name('notifications.approve-equipment-cost');
    Route::post('notifications/equipment-cost/{costId}/reject', [NotificationController::class, 'rejectEquipmentCost'])->name('notifications.reject-equipment-cost');
});

require __DIR__.'/auth.php';

// Broadcast routes for Pusher authentication
Broadcast::routes();
