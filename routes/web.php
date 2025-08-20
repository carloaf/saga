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

// Traditional Authentication routes
Route::get('/login/traditional', [AuthController::class, 'showLogin'])->name('auth.traditional-login');
Route::post('/login/traditional', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

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
    
    // Cardápio routes (apenas para superusers)
    Route::get('/cardapio', [App\Http\Controllers\CardapioController::class, 'index'])->name('cardapio.index');
    Route::get('/cardapio/edit', [App\Http\Controllers\CardapioController::class, 'edit'])->name('cardapio.edit');
    Route::put('/cardapio', [App\Http\Controllers\CardapioController::class, 'update'])->name('cardapio.update');
    Route::get('/cardapio/week/{week_start}', [App\Http\Controllers\CardapioController::class, 'getWeekMenu'])->name('cardapio.week');
    Route::get('/cardapio/suggestions/{week_start}', [App\Http\Controllers\CardapioController::class, 'getPreviousWeekSuggestions'])->name('cardapio.suggestions');
    
    // Furriel routes
    Route::middleware(['auth'])->prefix('furriel')->name('furriel.')->group(function () {
        Route::get('/debug', function() {
            return view('furriel.debug');
        })->name('debug');
        
        Route::get('/arranchamento-cia', [App\Http\Controllers\FurrielController::class, 'index'])->name('arranchamento.index');
        Route::post('/arranchamento-cia', [App\Http\Controllers\FurrielController::class, 'store'])->name('arranchamento.store');
        Route::get('/stats', [App\Http\Controllers\FurrielController::class, 'getStats'])->name('stats');
        
        // Rota de teste para AJAX
        Route::get('/test-ajax', function(Request $request) {
            return response()->json([
                'message' => 'AJAX funcionando',
                'date' => $request->get('date'),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'xhr_header' => $request->header('X-Requested-With')
            ]);
        })->name('test.ajax');
    });

    // Sgtte routes
    Route::middleware(['auth'])->prefix('sgtte')->name('sgtte.')->group(function () {
        Route::get('/servico', [App\Http\Controllers\SgtteController::class, 'index'])->name('servico');
        Route::post('/servico', [App\Http\Controllers\SgtteController::class, 'store'])->name('store');
    Route::get('/servico/bookings', [App\Http\Controllers\SgtteController::class, 'getBookings'])->name('servico.bookings');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        
        // Routes para relatórios
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
        Route::get('/reports/generate', [AdminController::class, 'generateReport'])->name('reports.generate');
    });
});

// DevTools for admin setup
Route::get('/dev-admin-login', function () {
    // Create a quick admin user for testing
    $admin = \App\Models\User::firstOrCreate(
        ['email' => 'admin@saga.mil.br'],
        [
            'google_id' => 'admin_dev_' . time(),
            'full_name' => 'Admin Development',
            'war_name' => 'ADMIN',
            'rank_id' => 1,
            'organization_id' => 1,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now(),
            'is_active' => true,
            'role' => 'manager'
        ]
    );
    
    Auth::login($admin);
    return redirect('/dashboard');
});

// DevTools for furriel testing
Route::get('/dev-furriel-login', function () {
    $furriel = \App\Models\User::where('role', 'furriel')->first();
    
    if ($furriel) {
        Auth::login($furriel);
        return redirect('/furriel/arranchamento-cia');
    } else {
        return 'Nenhum furriel encontrado no sistema';
    }
});

// DevTools for sgtte testing
Route::get('/dev-sgtte-login', function () {
    $sgtte = \App\Models\User::where('role', 'sgtte')->first();
    if ($sgtte) {
        Auth::login($sgtte);
        return redirect('/sgtte/servico');
    }
    return 'Nenhum sgtte encontrado. Execute SgtteSeeder.';
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
