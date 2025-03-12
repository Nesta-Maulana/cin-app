<?php

use App\Events\RoomChatBroadcast;
use App\Http\Controllers\Master\ApprovalController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\ItemCategoryController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Master\ItemTypeController;
use App\Http\Controllers\Master\ItemUomController;
use App\Http\Controllers\Master\JobCategoryController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\UnitOfMeasurementController;
use App\Http\Controllers\Master\WarehouseController;
use App\Http\Controllers\Master\WarehouseSectionController;
use App\Http\Controllers\Transaction\BomStatusController;
use App\Http\Controllers\Transaction\CustomerOrderController;
use App\Http\Controllers\Transaction\DeliveryOrderController;
use App\Http\Controllers\Transaction\ItemNeedToPurchaseController;
use App\Http\Controllers\Transaction\ItemRequestController;
use App\Http\Controllers\Transaction\ItemRequestProcessController;
use App\Http\Controllers\Transaction\PurchaseOrderController;
use App\Http\Controllers\Transaction\QuotationComparisonController;
use App\Http\Controllers\Transaction\StockEntryController;
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
    Route::get('/get-columns-by-model', [ApprovalController::class, 'getColumnsByModel'])->name('get-columns-by-model');
    Route::get('/get-references', [ApprovalController::class, 'getReferences'])->name('get-references');
    Route::post('/send-approval', [ApprovalController::class, 'sendApproval'])->name('send-approval');
    Route::resource('approval', ApprovalController::class);

    Route::resource('item-type', ItemTypeController::class);

    Route::get('/get-parent-categories', [ItemCategoryController::class, 'getParentCategories'])->name('get-parent-categories');
    Route::get('/get-categories-by-item-type', [ItemCategoryController::class, 'getCategoriesByItemType'])->name('get-categories-by-item-type');
    // API route for fetching categories
    Route::get('/get-item-categories', [ItemCategoryController::class, 'getCategories'])
        ->name('get-item-categories');
    Route::resource('item-category', ItemCategoryController::class);

    Route::resource('unit-of-measurement', UnitOfMeasurementController::class);
    Route::get('/get-uom-by-item', [ItemUomController::class, 'getUomByItem'])
        ->name('get-uom-by-item');
    Route::resource('item', ItemController::class);
    Route::resource('customer', CustomerController::class);
    Route::resource('job-category', JobCategoryController::class);
    Route::resource('customer-order', CustomerOrderController::class);

    Route::post('/item-request-approval', [ItemRequestController::class, 'approval'])->name('item-request-approval');
    Route::resource('item-request', ItemRequestController::class);

    Route::resource('department', DepartmentController::class);
    Route::get('/get-suppliers', [SupplierController::class, 'getSuppliers'])
        ->name('get-suppliers');
    Route::resource('supplier', SupplierController::class);
    Route::get('/get-section-by-warehouse-id', [WarehouseController::class, 'getSectionByWarehouseId'])
        ->name('get-section-by-warehouse-id');
    Route::get('/get-warehouse', [WarehouseController::class, 'getWarehouses'])
        ->name('get-warehouses');
    Route::resource('warehouse', WarehouseController::class);
    Route::resource('stock-entry', StockEntryController::class);

    Route::get('view-detail-item-request/{order}', [ItemRequestProcessController::class, 'viewDetailItemRequest'])->name('view-detail-item-request');

    Route::resource('item-request-process', ItemRequestProcessController::class);

    Route::get('/get-item-requests-by-customer-order', [DeliveryOrderController::class, 'getItemRequestsByCustomerOrder'])
        ->name('get-item-requests-by-customer-order');
    Route::get('/check-stock-availability', [DeliveryOrderController::class, 'checkStock'])->name('check-stock-availability');

    Route::get('delivery-order/notify-purchasing', [DeliveryOrderController::class, 'notifyPurchasing'])
        ->name('delivery-order.notify-purchasing');
    Route::resource('delivery-order', DeliveryOrderController::class);

    Route::get('/get-items-by-customer-order', [ItemNeedToPurchaseController::class, 'getItemByCustomerOrder'])
        ->name('get-items-by-customer-order');
    Route::resource('item-need-to-purchase', ItemNeedToPurchaseController::class);

    Route::get('/purchase-order/{purchaseOrder}/download-supplier-offers', [PurchaseOrderController::class, 'downloadSupplierOffers'])->name('download-supplier-offers');
    Route::post('/update-supplier-offer-selection', [PurchaseOrderController::class, 'updateSupplierOfferSelection'])
    ->name('update-supplier-offer-selection');

    Route::resource('bom-status', BomStatusController::class);
    Route::resource('quotation-comparison', QuotationComparisonController::class);
    Route::resource('purchase-order', PurchaseOrderController::class);
});
