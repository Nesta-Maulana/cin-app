<?php

namespace App\Providers;

use App\Models\ChatRoomDetail;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\Master\Contact\ContactRepository;
use App\Repositories\Master\Contact\ContactRepositoryInterface;
use App\Repositories\Master\Menu\MenuRepository;
use App\Repositories\Master\Menu\MenuRepositoryInterface;
use App\Repositories\Master\Permission\PermissionRepository;
use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use App\Repositories\Master\Role\RoleRepository;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use App\Repositories\Master\User\UserRepository;
use App\Repositories\Master\User\UserRepositoryInterface;
use App\Repositories\Transaction\Bot\BotRepository;
use App\Repositories\Transaction\Bot\BotRepositoryInterface;
use App\Repositories\Transaction\ChatList\ChatListRepository;
use App\Repositories\Transaction\ChatList\ChatListRepositoryInterface;
use App\Repositories\Transaction\ChatRoom\ChatRoomRepository;
use App\Repositories\Transaction\ChatRoom\ChatRoomRepositoryInterface;
use App\Repositories\Transaction\ChatRoomDetail\ChatRoomDetailRepository;
use App\Repositories\Transaction\ChatRoomDetail\ChatRoomDetailRepositoryInterface;
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
        $this->app->bind(BotRepositoryInterface::class, BotRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ChatListRepositoryInterface::class, ChatListRepository::class);
        $this->app->bind(ChatRoomRepositoryInterface::class, ChatRoomRepository::class);
        $this->app->bind(ChatRoomDetailRepositoryInterface::class, ChatRoomDetailRepository::class);
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
