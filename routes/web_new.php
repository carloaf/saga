<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Google OAuth routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/register/complete', [AuthController::class, 'showCompleteRegistration'])->name('register.complete');
Route::post('/register/complete', [AuthController::class, 'completeRegistration'])->name('register.complete.post');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/history', [BookingController::class, 'history'])->name('bookings.history');
    Route::post('/bookings/reserve-breakfast-week', [BookingController::class, 'reserveBreakfastWeek'])->name('bookings.reserve.breakfast.week');
    Route::post('/bookings/reserve-lunch-week', [BookingController::class, 'reserveLunchWeek'])->name('bookings.reserve.lunch.week');
    Route::post('/bookings/reserve-single', [BookingController::class, 'reserveSingle'])->name('bookings.reserve.single');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.cancel');
    Route::get('/bookings/user-bookings', [BookingController::class, 'getUserBookings'])->name('bookings.user');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/preferences', [App\Http\Controllers\ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::get('/profile/dashboard-data', [App\Http\Controllers\ProfileController::class, 'getDashboardData'])->name('profile.dashboard-data');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        
        Route::get('/reports', function () {
            // Verificação simples de role
            if (!Auth::user() || Auth::user()->role !== 'superuser') {
                abort(403, 'Acesso negado. Apenas superusuários podem acessar esta área.');
            }
            
            return view('admin.reports.index');
        })->name('reports.index');
    });
});

// DevTools for admin setup
Route::get('/dev-admin-login', function () {
    // Create a quick admin user for testing
    $admin = \App\Models\User::firstOrCreate(
        ['email' => 'admin@saga.test'],
        [
            'full_name' => 'Administrador Sistema',
            'war_name' => 'ADMIN',
            'google_id' => 'dev-admin-123',
            'email_verified_at' => now(),
            'role' => 'superuser',
            'is_active' => true,
            'rank_id' => 1,
            'organization_id' => 1,
            'gender' => 'male',
            'ready_at_om_date' => now()->format('Y-m-d'),
        ]
    );
    
    Auth::login($admin);
    return redirect('/admin/users');
});

Route::get('/setup-admin', function () {
    // Ensure we have at least one rank and organization
    if (!\App\Models\Rank::exists()) {
        \App\Models\Rank::create(['name' => 'Capitão', 'abbreviation' => 'Cap']);
    }
    
    if (!\App\Models\Organization::exists()) {
        \App\Models\Organization::create(['name' => 'Comando Geral', 'abbreviation' => 'CG']);
    }
    
    return "Setup completo! Acesse <a href='/dev-admin-login'>Dev Admin Login</a>";
});

// Test routes
Route::get('/test-layout', function () {
    return view('test-layout');
});

Route::get('/test-timezone', function () {
    return view('test-timezone');
});

// Fallback route
Route::fallback(function () {
    return redirect('/');
});
