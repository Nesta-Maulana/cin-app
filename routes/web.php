<?php

use App\Events\RoomChatBroadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\MenuController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SettingController;
use App\Http\Controllers\Master\DashboardController;
use App\Http\Controllers\Master\MyAccountController;
use App\Http\Controllers\Master\ManageUserController;
use App\Http\Controllers\Master\PermissionController;

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

Auth::routes();
Route::get("/test-websocket", function () {
    RoomChatBroadcast::dispatch('test');
});
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'auth.status'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('my-account', [MyAccountController::class, 'edit'])->name('my-account.edit');
    Route::put('my-account', [MyAccountController::class, 'update'])->name('my-account.update');
    Route::get('my-account/log-activity', [MyAccountController::class, 'logActivity'])->name('my-account.log-activity');

    Route::resource('user', ManageUserController::class);
    Route::put('/user/{id}/password', [ManageUserController::class, 'updatePassword'])->name('user.update.password');

    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('setting', SettingController::class);


});
