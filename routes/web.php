<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,15');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')->middleware('auth');

// ─── Locale Switch ────────────────────────────────────────────────
Route::post('/locale/switch', function (\Illuminate\Http\Request $request) {
    $locale = in_array($request->locale, ['en', 'ur']) ? $request->locale : 'en';
    session(['locale' => $locale]);
    if (auth()->check() && auth()->user()->role === 'admin') {
        \App\Services\SettingService::set('app_locale', $locale);
    }
    return response()->json(['switched' => true]);
})->name('locale.switch');

// ─── Root redirect ────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// ─── Role-based portals ───────────────────────────────────────────
require __DIR__ . '/admin.php';
require __DIR__ . '/teacher.php';
require __DIR__ . '/student.php';
