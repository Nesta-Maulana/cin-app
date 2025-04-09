<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderNewDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relationship to the parent purchase order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderNew::class, 'purchase_order_id');
    }

    // Relationship to the quotation comparison detail
    public function quotationComparisonDetail()
    {
        return $this->belongsTo(QuotationComparisonDetail::class);
    }

    // Relationship to the item request detail
    public function itemRequestDetail()
    {
        return $this->belongsTo(ItemRequestDetail::class);
    }

    // Relationship to the manual item request detail
    public function manualItemRequestDetail()
    {
        return $this->belongsTo(ManualItemRequestDetail::class);
    }

    // Relationship to the item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship to the item UOM
    public function itemUom()
    {
        return $this->belongsTo(ItemUom::class);
    }

    // Get the unit of measurement directly
    public function unitOfMeasurement()
    {
        return $this->itemUom ? $this->itemUom->unitOfMeasurement : null;
    }

    // Scope for filtering by item name
    public function scopeByItemName($query, $itemName)
    {
        return $query->where('item_name', 'like', "%$itemName%");
    }
}
