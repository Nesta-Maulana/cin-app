<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class PrePurchaseOrderDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relasi ke Purchase Order
    public function prePurchaseOrder()
    {
        return $this->belongsTo(PrePurchaseOrder::class, 'pre_purchase_order_id');
    }

    // Relasi ke Item
    public function itemRequestDetail()
    {
        return $this->belongsTo(ItemRequestDetail::class, 'item_request_detail_id');
    }
    public function manualItemRequestDetail()
    {
        return $this->belongsTo(ManualItemRequestDetail::class);
    }
    // Relasi ke Satuan Unit of Measurement (UOM)
    public function uom()
    {
        return $this->belongsTo(ItemUom::class, 'item_uom_id');
    }

    // Relasi ke supplier offers
    public function quotations()
    {
        return $this->hasMany(QuotationComparisonDetail::class, 'pre_purchase_order_detail_id');
    }

    // Scope untuk filter berdasarkan item
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        });
    }
}
