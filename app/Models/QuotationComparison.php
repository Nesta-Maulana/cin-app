<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    public function prePurchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PrePurchaseOrder::class);
    }

    /**
     * Get the supplier that owns the quotation
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the quotation details for the quotation
     */
    public function quotationDetails(): HasMany
    {
        return $this->hasMany(QuotationComparisonDetail::class);
    }
    public function details(): HasMany
    {
        return $this->hasMany(QuotationComparisonDetail::class);
    }

    /**
     * Get all additional costs for the quotation
     */
    public function additionalCosts(): HasMany
    {
        return $this->hasMany(QuotationComparisonAdditionalCost::class);
    }

    /**
     * Get the before tax costs for the quotation
     */
    public function beforeTaxCosts()
    {
        return $this->additionalCosts()->where('category', 'before_tax');
    }

    /**
     * Get the after tax costs for the quotation
     */
    public function afterTaxCosts()
    {
        return $this->additionalCosts()->where('category', 'after_tax');
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
