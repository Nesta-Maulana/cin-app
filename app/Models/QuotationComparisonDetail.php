<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class QuotationComparisonDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relasi ke Purchase Order Supplier Offer
    public function quotationComparison(): BelongsTo
    {
        return $this->belongsTo(QuotationComparison::class);
    }

    /**
     * Get the pre purchase order detail that owns the detail
     */
    public function prePurchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PrePurchaseOrderDetail::class);
    }

    /**
     * Get the item uom that owns the detail
     */
    public function itemUom(): BelongsTo
    {
        return $this->belongsTo(ItemUom::class);
    }

    /**
     * Get the item through item uom
     */
    public function item()
    {
        return $this->itemUom->item();
    }
    public function unitOfMeasurement()
    {
        return $this->itemUom->unitOfMeasurement();
    }


    // Scope untuk filter berdasarkan harga
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->where('offered_price_per_unit', '<=', $search);
        });
    }
}
