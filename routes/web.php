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

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\UserController;

Route::get('/profile', [UserController::class, 'profile'])->middleware(['auth'])->name('profile');
Route::post('/api-tokens', [ApiTokenController::class, 'store'])->middleware(['auth'])->name('api-tokens.store');
Route::delete('/api-tokens/{id}', [ApiTokenController::class, 'destroy'])->middleware(['auth'])->name('api-tokens.destroy');

Route::resource('citizens', CitizenController::class)
    ->middleware(['auth']);

Route::get('audits', [AuditController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('audits.index');

Route::resource('users', UserController::class)
    ->middleware(['auth', 'role:admin']);

Route::get('citizens/export/csv', [CitizenController::class, 'exportCsv'])->name('citizens.export.csv');
Route::post('citizens/{citizen}/face', [CitizenController::class, 'storeFace'])->name('citizens.face.store');

// Example of a dashboard route if you had one
// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
