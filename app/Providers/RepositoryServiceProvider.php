<?php

namespace App\Providers;

use App\Models\ChatRoomDetail;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\Master\City\CityRepository;
use App\Repositories\Master\City\CityRepositoryInterface;
use App\Repositories\Master\Material\MaterialRepository;
use App\Repositories\Master\Material\MaterialRepositoryInterface;
use App\Repositories\Master\Menu\MenuRepository;
use App\Repositories\Master\Menu\MenuRepositoryInterface;
use App\Repositories\Master\Permission\PermissionRepository;
use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use App\Repositories\Master\Role\RoleRepository;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use App\Repositories\Master\Unit\UnitRepositoryInterface;
use App\Repositories\Master\Unit\UnitRepository;
use App\Repositories\Master\User\UserRepository;
use App\Repositories\Master\User\UserRepositoryInterface;
use App\Repositories\Transaction\BOM\BOMRepository;
use App\Repositories\Transaction\BOM\BOMRepositoryInterface;
use App\Repositories\Transaction\PurchaseRequest\PurchaseRequestRepository;
use App\Repositories\Transaction\PurchaseRequest\PurchaseRequestRepositoryInterface;
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
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(BOMRepositoryInterface::class, BOMRepository::class);
        $this->app->bind(PurchaseRequestRepositoryInterface::class, PurchaseRequestRepository::class);
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
