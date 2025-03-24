<?php

namespace App\Providers;

use App\Models\PurchaseOrderSupplierOfferDetail;
use App\Models\Warehouse;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\Master\Approval\ApprovalRepository;
use App\Repositories\Master\Approval\ApprovalRepositoryInterface;
use App\Repositories\Master\ApprovalLevel\ApprovalLevelRepository;
use App\Repositories\Master\ApprovalLevel\ApprovalLevelRepositoryInterface;
use App\Repositories\Master\Customer\CustomerRepository;
use App\Repositories\Master\Customer\CustomerRepositoryInterface;
use App\Repositories\Master\Department\DepartmentRepository;
use App\Repositories\Master\Department\DepartmentRepositoryInterface;
use App\Repositories\Master\Item\ItemRepository;
use App\Repositories\Master\Item\ItemRepositoryInterface;
use App\Repositories\Master\ItemCategory\ItemCategoryRepository;
use App\Repositories\Master\ItemCategory\ItemCategoryRepositoryInterface;
use App\Repositories\Master\ItemPriceHistory\ItemPriceHistoryRepository;
use App\Repositories\Master\ItemPriceHistory\ItemPriceHistoryRepositoryInterface;
use App\Repositories\Master\ItemType\ItemTypeRepository;
use App\Repositories\Master\ItemType\ItemTypeRepositoryInterface;
use App\Repositories\Master\ItemUom\ItemUomRepository;
use App\Repositories\Master\ItemUom\ItemUomRepositoryInterface;
use App\Repositories\Master\JobCategory\JobCategoryRepository;
use App\Repositories\Master\JobCategory\JobCategoryRepositoryInterface;
use App\Repositories\Master\Menu\MenuRepository;
use App\Repositories\Master\Menu\MenuRepositoryInterface;
use App\Repositories\Master\Permission\PermissionRepository;
use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use App\Repositories\Master\Role\RoleRepository;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use App\Repositories\Master\Supplier\SupplierRepository;
use App\Repositories\Master\Supplier\SupplierRepositoryInterface;
use App\Repositories\Master\UnitOfMeasurement\UnitOfMeasurementRepository;
use App\Repositories\Master\UnitOfMeasurement\UnitOfMeasurementRepositoryInterface;
use App\Repositories\Master\User\UserRepository;
use App\Repositories\Master\User\UserRepositoryInterface;
use App\Repositories\Master\Warehouse\WarehouseRepository;
use App\Repositories\Master\Warehouse\WarehouseRepositoryInterface;
use App\Repositories\Master\WarehouseSection\WarehouseSectionRepository;
use App\Repositories\Master\WarehouseSection\WarehouseSectionRepositoryInterface;
use App\Repositories\Services\File\FileRepository;
use App\Repositories\Services\File\FileRepositoryInterface;
use App\Repositories\Transaction\ApprovalRequest\ApprovalRequestRepository;
use App\Repositories\Transaction\ApprovalRequest\ApprovalRequestRepositoryInterface;
use App\Repositories\Transaction\BomStatus\BomStatusRepository;
use App\Repositories\Transaction\BomStatus\BomStatusRepositoryInterface;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepository;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\Transaction\DeliveryOrder\DeliveryOrderRepository;
use App\Repositories\Transaction\DeliveryOrder\DeliveryOrderRepositoryInterface;
use App\Repositories\Transaction\DeliveryOrderDetail\DeliveryOrderDetailRepository;
use App\Repositories\Transaction\DeliveryOrderDetail\DeliveryOrderDetailRepositoryInterface;
use App\Repositories\Transaction\ItemNeedToPurchase\ItemNeedToPurchaseRepository;
use App\Repositories\Transaction\ItemNeedToPurchase\ItemNeedToPurchaseRepositoryInterface;
use App\Repositories\Transaction\ItemNeedToPurchaseDetail\ItemNeedToPurchaseDetailRepository;
use App\Repositories\Transaction\ItemNeedToPurchaseDetail\ItemNeedToPurchaseDetailRepositoryInterface;
use App\Repositories\Transaction\ItemRequest\ItemRequestRepository;
use App\Repositories\Transaction\ItemRequest\ItemRequestRepositoryInterface;
use App\Repositories\Transaction\ItemRequestDetail\ItemRequestDetailRepository;
use App\Repositories\Transaction\ItemRequestDetail\ItemRequestDetailRepositoryInterface;
use App\Repositories\Transaction\ItemRequestProcess\ItemRequestProcessRepository;
use App\Repositories\Transaction\ItemRequestProcess\ItemRequestProcessRepositoryInterface;
use App\Repositories\Transaction\ManualItemRequest\ManualItemRequestRepository;
use App\Repositories\Transaction\ManualItemRequest\ManualItemRequestRepositoryInterface;
use App\Repositories\Transaction\ManualItemRequestDetail\ManualItemRequestDetailRepository;
use App\Repositories\Transaction\ManualItemRequestDetail\ManualItemRequestDetailRepositoryInterface;
use App\Repositories\Transaction\PrePurchaseOrder\PrePurchaseOrderRepository;
use App\Repositories\Transaction\PrePurchaseOrder\PrePurchaseOrderRepositoryInterface;
use App\Repositories\Transaction\PrePurchaseOrderDetail\PrePurchaseOrderDetailRepository;
use App\Repositories\Transaction\PrePurchaseOrderDetail\PrePurchaseOrderDetailRepositoryInterface;
use App\Repositories\Transaction\PurchaseOrder\PurchaseOrderRepository;
use App\Repositories\Transaction\PurchaseOrder\PurchaseOrderRepositoryInterface;
use App\Repositories\Transaction\PurchaseOrderDetail\PurchaseOrderDetailRepository;
use App\Repositories\Transaction\PurchaseOrderDetail\PurchaseOrderDetailRepositoryInterface;
use App\Repositories\Transaction\PurchaseOrderSupplierOffer\PurchaseOrderSupplierOfferRepository;
use App\Repositories\Transaction\PurchaseOrderSupplierOffer\PurchaseOrderSupplierOfferRepositoryInterface;
use App\Repositories\Transaction\PurchaseOrderSupplierOfferDetail\PurchaseOrderSupplierOfferDetailRepository;
use App\Repositories\Transaction\PurchaseOrderSupplierOfferDetail\PurchaseOrderSupplierOfferDetailRepositoryInterface;
use App\Repositories\Transaction\QuotationComparison\QuotationComparisonRepository;
use App\Repositories\Transaction\QuotationComparison\QuotationComparisonRepositoryInterface;
use App\Repositories\Transaction\QuotationComparisonAdditionalCost\QuotationComparisonAdditionalCostRepository;
use App\Repositories\Transaction\QuotationComparisonAdditionalCost\QuotationComparisonAdditionalCostRepositoryInterface;
use App\Repositories\Transaction\QuotationComparisonDetail\QuotationComparisonDetailRepository;
use App\Repositories\Transaction\QuotationComparisonDetail\QuotationComparisonDetailRepositoryInterface;
use App\Repositories\Transaction\QuotationComparisonShippingCost\QuotationComparisonShippingCostRepository;
use App\Repositories\Transaction\QuotationComparisonShippingCost\QuotationComparisonShippingCostRepositoryInterface;
use App\Repositories\Transaction\StockEntry\StockEntryRepository;
use App\Repositories\Transaction\StockEntry\StockEntryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(ItemTypeRepositoryInterface::class, ItemTypeRepository::class);
        $this->app->bind(ItemCategoryRepositoryInterface::class, ItemCategoryRepository::class);
        $this->app->bind(UnitOfMeasurementRepositoryInterface::class, UnitOfMeasurementRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
        $this->app->bind(ItemUomRepositoryInterface::class, ItemUomRepository::class);
        $this->app->bind(ItemPriceHistoryRepositoryInterface::class, ItemPriceHistoryRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(JobCategoryRepositoryInterface::class, JobCategoryRepository::class);
        $this->app->bind(CustomerOrderRepositoryInterface::class, CustomerOrderRepository::class);
        $this->app->bind(ItemRequestRepositoryInterface::class, ItemRequestRepository::class);
        $this->app->bind(ItemRequestDetailRepositoryInterface::class, ItemRequestDetailRepository::class);
        $this->app->bind(ApprovalRepositoryInterface::class, ApprovalRepository::class);
        $this->app->bind(ApprovalLevelRepositoryInterface::class, ApprovalLevelRepository::class);
        $this->app->bind(ApprovalRequestRepositoryInterface::class, ApprovalRequestRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(WarehouseSectionRepositoryInterface::class, WarehouseSectionRepository::class);
        $this->app->bind(StockEntryRepositoryInterface::class, StockEntryRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(ItemRequestProcessRepositoryInterface::class, ItemRequestProcessRepository::class);
        $this->app->bind(DeliveryOrderRepositoryInterface::class, DeliveryOrderRepository::class);
        $this->app->bind(DeliveryOrderDetailRepositoryInterface::class, DeliveryOrderDetailRepository::class);
        $this->app->bind(ItemNeedToPurchaseRepositoryInterface::class, ItemNeedToPurchaseRepository::class);
        $this->app->bind(ItemNeedToPurchaseDetailRepositoryInterface::class, ItemNeedToPurchaseDetailRepository::class);
        $this->app->bind(PurchaseOrderRepositoryInterface::class, PurchaseOrderRepository::class);
        $this->app->bind(PurchaseOrderDetailRepositoryInterface::class, PurchaseOrderDetailRepository::class);
        $this->app->bind(PurchaseOrderSupplierOfferRepositoryInterface::class, PurchaseOrderSupplierOfferRepository::class);
        $this->app->bind(PurchaseOrderSupplierOfferDetailRepositoryInterface::class, PurchaseOrderSupplierOfferDetailRepository::class);

        // bom status
        $this->app->bind(BomStatusRepositoryInterface::class, BomStatusRepository::class);

        $this->app->bind(PrePurchaseOrderRepositoryInterface::class, PrePurchaseOrderRepository::class);
        $this->app->bind(PrePurchaseOrderDetailRepositoryInterface::class, PrePurchaseOrderDetailRepository::class);
        $this->app->bind(QuotationComparisonRepositoryInterface::class, QuotationComparisonRepository::class);
        $this->app->bind(QuotationComparisonDetailRepositoryInterface::class, QuotationComparisonDetailRepository::class);
        $this->app->bind(QuotationComparisonAdditionalCostRepositoryInterface::class, QuotationComparisonAdditionalCostRepository::class);
        $this->app->bind(ManualItemRequestRepositoryInterface::class, ManualItemRequestRepository::class);
        $this->app->bind(ManualItemRequestDetailRepositoryInterface::class, ManualItemRequestDetailRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
