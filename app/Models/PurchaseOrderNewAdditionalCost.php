<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderNewAdditionalCost extends Model
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

    // Scope for filtering by cost type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope for filtering by category (before/after tax)
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope for getting before-tax costs
    public function scopeBeforeTax($query)
    {
        return $query->where('category', 'before_tax');
    }

    // Scope for getting after-tax costs
    public function scopeAfterTax($query)
    {
        return $query->where('category', 'after_tax');
    }
}
