<?php

use App\Events\RoomChatBroadcast;
use App\Http\Controllers\Master\ItemCategoryController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Master\ItemTypeController;
use App\Http\Controllers\Master\MaterialController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\UnitOfMeasurementController;
use App\Http\Controllers\Transaction\BOMController;
use App\Http\Controllers\Transaction\PurchaseRequestController;
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

    Route::resource('item-type', ItemTypeController::class);

    Route::get('/get-parent-categories', [ItemCategoryController::class, 'getParentCategories'])->name('get-parent-categories');
    Route::get('/get-categories-by-item-type', [ItemCategoryController::class, 'getCategoriesByItemType'])->name('get-categories-by-item-type');
    Route::resource('item-category', ItemCategoryController::class);

    Route::resource('unit-of-measurement', UnitOfMeasurementController::class);
    Route::resource('item', ItemController::class);

});
