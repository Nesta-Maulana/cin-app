<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relasi ke Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    // Relasi ke Item
    public function itemRequestDetail()
    {
        return $this->belongsTo(ItemRequestDetail::class, 'item_request_detail_id');
    }

    // Relasi ke Satuan Unit of Measurement (UOM)
    public function uom()
    {
        return $this->belongsTo(ItemUom::class, 'item_uom_id');
    }

    // Relasi ke supplier offers
    public function supplierOffers()
    {
        return $this->hasMany(PurchaseOrderSupplierOffer::class, 'purchase_order_detail_id');
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
