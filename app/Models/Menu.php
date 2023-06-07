<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function scopeFilter($query, $search) 
    {
        $query->when($search ?? false, function($query, $search){
            return $query->where('name', 'like', "%$search%");
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
        return $this->belongsTo(Menu::class, 'main_menu');
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
        return $this->subMenu()->count() > 0;
    }
}
 