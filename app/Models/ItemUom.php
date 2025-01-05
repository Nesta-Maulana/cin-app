<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ItemUom extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
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

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function unitOfMeasurement()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }

    public function itemPriceHistories()
    {
        return $this->hasMany(ItemPriceHistory::class, 'item_uom_id', 'id');
    }


    public function latestPrice()
    {
        return $this->hasOne(ItemPriceHistory::class)->where('is_active', 1)->latestOfMany('created_at');
    }
}
