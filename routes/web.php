<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MyAccountController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\PermissionController;

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

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::prefix('admin')->middleware(['auth', 'auth.status'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('my-account', [MyAccountController::class, 'edit'])->name('my-account.edit');
    Route::put('my-account', [MyAccountController::class, 'update'])->name('my-account.update');

    Route::resource('user-admin', UserAdminController::class);
    Route::put('/user-admin/{id}/password', [UserAdminController::class, 'updatePassword'])->name('user-admin.update.password');
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('setting', SettingController::class);

});
