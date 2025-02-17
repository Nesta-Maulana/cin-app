<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderSupplierOffer extends Model
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

    // Relasi ke Purchase Order Detail (barang yang ditawarkan)
    public function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relasi ke detail offer supplier
    public function offerDetails()
    {
        return $this->hasMany(PurchaseOrderSupplierOfferDetail::class, 'offer_id');
    }

    // Scope untuk filter supplier
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->whereHas('supplier', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        });
    }
}
