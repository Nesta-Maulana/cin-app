<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Menu extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearMenuCache();
        });

        static::deleted(function () {
            static::clearMenuCache();
        });
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public static function clearMenuCache()
    {
        Cache::forget('menu');
    }

    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('name', 'ilike', "%$search%");
        });
    }

    /**
     * Get the permission that owns the Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    /**
     * Get the menu that owns the Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'main_menu','id');
    }

    /**
     * Get all of the subMenus for the Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subMenu()
    {
        return $this->hasMany(Menu::class, 'main_menu')->orderBy('sort', 'asc');
    }

    public function hasSubMenu()
    {
        return $this->subMenu()->count('id') > 0;
    }

    public function scopeWithIcon($query)
    {
        return $query->whereNotNull('icon');
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort', 'asc');
    }
    public function scopeJustParent($query)
    {
        return $query->whereNull('main_menu');
    }
}
