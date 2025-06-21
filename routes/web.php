<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BulkDealController;
use Illuminate\Support\Facades\Route;

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
    Route::resource('cars', CarController::class)->parameters(['cars' => 'car']);
    Route::post('cars/{car}/equipment-costs', [CarController::class, 'addEquipmentCost'])->name('cars.add-equipment-cost');
    Route::post('cars/{car}/update-status', [CarController::class, 'updateStatus'])->name('cars.update-status');
    Route::delete('cars/{car}/images', [CarController::class, 'deleteImage'])->name('cars.delete-image');
    
    // Bulk Deal routes
    Route::resource('bulk-deals', BulkDealController::class);
});

require __DIR__.'/auth.php';
