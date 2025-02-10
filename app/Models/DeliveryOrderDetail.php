<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeliveryOrderDetail extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that aren't mass assignable.
     * Only 'id' is guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }


    /**
     * Get the delivery order header that owns the detail.
     */
    public function header()
    {
        return $this->belongsTo(DeliveryOrder::class, 'header_id');
    }

    /**
     * Get the item request that is associated with this detail (if any).
     */
    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    /**
     * Get the item associated with this detail.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the unit of measurement for the item in this detail.
     */
    public function itemUom()
    {
        return $this->belongsTo(ItemUom::class, 'item_uom_id');
    }

    /**
     * Get the warehouse where the item is delivered.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the warehouse section where the item is delivered.
     */
    public function section()
    {
        return $this->belongsTo(WarehouseSection::class, 'section_id');
    }
}
