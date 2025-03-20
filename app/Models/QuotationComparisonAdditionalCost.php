<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class QuotationComparisonAdditionalCost extends Model
{
      /**
     * Get the quotation comparison that owns the additional cost
     */
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }


    public function quotationComparison(): BelongsTo
    {
        return $this->belongsTo(QuotationComparison::class);
    }
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->where('offered_price_per_unit', '<=', $search);
        });
    }
}
