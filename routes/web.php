<?php

use App\Http\Controllers\CitizenController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard');

Route::resource('citizens', CitizenController::class)
    ->middleware(['auth']);

Route::post('citizens/{citizen}/face', [CitizenController::class, 'storeFace'])->name('citizens.face.store');
Route::post('citizens/{citizen}/fingerprint', [CitizenController::class, 'storeFingerprint'])->name('citizens.fingerprint.store');

// Example of a dashboard route if you had one
// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
