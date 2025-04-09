<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderNew extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relationship to the supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship to the customer order
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    // Relationship to the pre-purchase order
    public function prePurchaseOrder()
    {
        return $this->belongsTo(PrePurchaseOrder::class);
    }

    // Relationship to purchase order details
    public function details()
    {
        return $this->hasMany(PurchaseOrderNewDetail::class, 'purchase_order_id');
    }

    // Relationship to additional costs
    public function additionalCosts()
    {
        return $this->hasMany(PurchaseOrderNewAdditionalCost::class, 'purchase_order_id');
    }

    // Relationship to attachments
    public function attachments()
    {
        return $this->hasMany(PurchaseOrderNewAttachment::class, 'purchase_order_id');
    }

    // Relationship to status history
    public function statusHistory()
    {
        return $this->hasMany(PurchaseOrderNewStatusHistory::class, 'purchase_order_id');
    }

    // Relationship to the user who created the PO
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship to the user who last updated the PO
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relationship to the user who approved the PO
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('process_status', $status);
    }

    // Scope for filtering by supplier
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    // General search scope
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->where('po_number', 'like', "%$search%")
                ->orWhereHas('supplier', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('customerOrder', function ($q) use ($search) {
                    $q->where('order_number', 'like', "%$search%")
                    ->orWhere('project_name', 'like', "%$search%");
                });
        });
    }

    // Get the approval request
    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }
}
