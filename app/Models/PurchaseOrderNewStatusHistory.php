<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderNewStatusHistory extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];
    public $timestamps = false;
    protected $fillable = [
        'purchase_order_id',
        'from_status',
        'to_status',
        'remarks',
        'changed_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relationship to the parent purchase order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderNew::class, 'purchase_order_id');
    }

    // Relationship to the user who changed the status
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scope for filtering by status transition
    public function scopeByTransition($query, $fromStatus, $toStatus)
    {
        return $query->where('from_status', $fromStatus)
            ->where('to_status', $toStatus);
    }

    // Scope for filtering by date range
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
