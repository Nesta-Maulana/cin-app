<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PrePurchaseOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relasi ke detail PO
    public function details()
    {
        return $this->hasMany(PrePurchaseOrderDetail::class, 'pre_purchase_order_id');
    }

    // Relasi ke offer supplier
    public function quotations()
    {
        return $this->hasMany(QuotationComparison::class, 'pre_purchase_order_id');
    }

    // Relasi ke customer order (opsional, jika PO terkait customer order tertentu)
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    // Relasi ke user yang membuat PO
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Scope filter untuk pencarian
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->where('process_number', 'like', "%$search%");
        });
    }
    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }
}
