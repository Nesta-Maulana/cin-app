<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Item extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', 1);
        });
    }
    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        });
    }

    public function getStatusAttribute()
    {
        if ($this->is_active == 0) {
            return '<span class="badge rounded-pill badge-light-secondary">Inactive</span>';
        } else {
            return '<span class="badge rounded-pill badge-light-success">Active</span>';
        }
    }

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }
    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }
    public function unitOfMeasurement()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }
    public function itemUoms()
    {
        return $this->hasMany(ItemUom::class, 'item_id', 'id')->where('is_active', 1);
    }
    public function itemPriceHistories()
    {
        return $this->hasMany(ItemPriceHistory::class, 'item_id', 'id');
    }
    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseSectionStock::class, 'item_id', 'id');
    }


}
