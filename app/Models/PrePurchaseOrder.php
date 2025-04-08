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

    // Relationship to details
    // 与详情的关系
    public function details()
    {
        return $this->hasMany(PrePurchaseOrderDetail::class, 'pre_purchase_order_id');
    }

    // Relationship to supplier quotations
    // 与供应商报价的关系
    public function quotations()
    {
        return $this->hasMany(QuotationComparison::class, 'pre_purchase_order_id');
    }

    // Relationship to customer order
    // 与客户订单的关系
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    // Relationship to the user who created the order
    // 与创建订单的用户的关系
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship to the user who approved the order
    // 与批准订单的用户的关系
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relationship to the user who finalized the order
    // 与最终确定订单的用户的关系
    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    // Relationship to item-specific supplier selections
    // 与特定物品供应商选择的关系
    public function itemSelections()
    {
        return $this->hasMany(PrePurchaseOrderItemSelection::class);
    }

    // Get the selected quotation
    // 获取所选的报价
    public function selectedQuotation()
    {
        return $this->quotations()->where('is_selected', true)->first();
    }

    // Get the selected supplier for a specific item
    // 获取特定物品的所选供应商
    public function getSelectedSupplierForItem($itemKey)
    {
        $selection = $this->itemSelections()->where('item_key', $itemKey)->first();
        return $selection ? $selection->quotation : null;
    }

    // Scope for filtering
    // 用于过滤的作用域
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->where('pre_po_number', 'like', "%$search%");
        });
    }

    // Check for approval
    // 检查审批
    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }
    public function approvalRequests()
    {
        return $this->morphMany(ApprovalRequest::class, 'reference', 'class_name', 'reference_id');
    }

    public function getLatestApprovalRequest()
    {
        return $this->approvalRequests()
            ->with([
                'logs' => function ($query) {
                    $query->latest();
                }
            ])
            ->latest()
            ->first();
    }

}
