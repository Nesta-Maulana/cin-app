<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class QuotationComparison extends Model
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

    // Relasi ke Purchase Order Detail (barang yang ditawarkan)
    public function prePurchaseOrderDetail()
    {
        return $this->belongsTo(PrePurchaseOrderDetail::class, 'pre_purchase_order_detail_id');
    }


    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relasi ke detail offer supplier
    public function quotationDetails()
    {
        return $this->hasMany(QuotationComparisonDetail::class, 'offer_id');
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
