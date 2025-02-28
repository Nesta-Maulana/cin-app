<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderSupplierOfferDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relasi ke Purchase Order Supplier Offer
    public function offer()
    {
        return $this->belongsTo(PurchaseOrderSupplierOffer::class, 'offer_id');
    }

    // Relasi ke Item
    public function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }

    // Relasi ke Satuan Unit of Measurement (UOM)
    public function uom()
    {
        return $this->belongsTo(ItemUom::class, 'item_uom_id');
    }

    // Scope untuk filter berdasarkan harga
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->where('offered_price_per_unit', '<=', $search);
        });
    }
}
