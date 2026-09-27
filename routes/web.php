<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\RoomManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Staff\FrontDeskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [RoomController::class, 'index'])->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------
    | Booking engine (any authenticated guest)
    |--------------------------------------------------------------------
    */
    Route::get('/rooms/{room}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/rooms/{room}/availability', [BookingController::class, 'checkAvailability'])->name('bookings.availability');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');

    /*
    |--------------------------------------------------------------------
    | Customer dashboard
    |--------------------------------------------------------------------
    */
    Route::prefix('account')->name('customer.')->middleware('role:customer,admin,staff')->group(function () {
        Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [CustomerDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    });

    /*
    |--------------------------------------------------------------------
    | Staff / Front desk
    |--------------------------------------------------------------------
    */
    Route::prefix('staff')->name('staff.')->middleware('role:staff,admin')->group(function () {
        Route::get('/', [FrontDeskController::class, 'dashboard'])->name('dashboard');
        Route::post('/walk-in', [FrontDeskController::class, 'storeWalkIn'])->name('walkin.store');
        Route::post('/bookings/{booking}/check-in', [FrontDeskController::class, 'checkIn'])->name('bookings.checkin');
        Route::post('/bookings/{booking}/check-out', [FrontDeskController::class, 'checkOut'])->name('bookings.checkout');
        Route::post('/rooms/{room}/status', [FrontDeskController::class, 'updateRoomStatus'])->name('rooms.status');
    });

    /*
    |--------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('dashboard');
        Route::get('/export/bookings.csv', [AnalyticsController::class, 'exportCsv'])->name('export.bookings');

        Route::resource('rooms', RoomManagementController::class)->except(['show']);
        Route::post('/rooms/{room}/status', [RoomManagementController::class, 'updateStatus'])->name('rooms.updateStatus');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggleActive');
    });
});
