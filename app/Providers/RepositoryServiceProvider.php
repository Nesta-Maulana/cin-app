<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
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
use App\Repositories\Master\Menu\MenuRepository;
use App\Repositories\Master\Menu\MenuRepositoryInterface;
use App\Repositories\Master\Permission\PermissionRepository;
use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use App\Repositories\Master\Role\RoleRepository;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use App\Repositories\Master\UnitOfMeasurement\UnitOfMeasurementRepository;
use App\Repositories\Master\UnitOfMeasurement\UnitOfMeasurementRepositoryInterface;
use App\Repositories\Master\User\UserRepository;
use App\Repositories\Master\User\UserRepositoryInterface;
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
