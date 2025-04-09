<?php

namespace App\Models;

use DB;
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
    public function purchaseOrderNews()
    {
        return $this->hasMany(PurchaseOrderNew::class, 'pre_purchase_order_id');
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
    public function getSelectedSuppliersAttribute()
    {
        // Get supplier IDs from item selections via their quotations
        $supplierIds = $this->itemSelections()
            ->join('quotation_comparisons', 'pre_purchase_order_item_selections.quotation_id', '=', 'quotation_comparisons.id')
            ->pluck('quotation_comparisons.supplier_id')
            ->unique()
            ->values();

        // If we have a selected quotation at the pre-purchase order level, include that supplier too
        $selectedQuotation = $this->selectedQuotation();
        if ($selectedQuotation && $selectedQuotation->supplier_id) {
            $supplierIds = $supplierIds->add($selectedQuotation->supplier_id)->unique();
        }

        // Return the suppliers
        return Supplier::whereIn('id', $supplierIds)->get();
    }

    /**
     * Check if a purchase order can be created for a specific supplier
     *
     * @param int $supplierId
     * @return bool
     */
    /**
     * Check if a purchase order can be created for a specific supplier
     *
     * @param int $supplierId
     * @return bool
     */
    /**
     * Check if a purchase order can be created for a specific supplier
     *
     * @param int $supplierId
     * @return bool
     */
    public function canCreatePOForSupplier($supplierId)
    {
        // Check if a PO already exists for this pre-purchase order and supplier
        $existingPO = $this->purchaseOrderNews()
            ->where('supplier_id', $supplierId)
            ->count();

        // If no PO exists yet, check if the supplier is selected
        if ($existingPO == 0) {
            // Check if supplier is selected in item selections
            $isSelectedInItemSelections = $this->itemSelections()
                ->join('quotation_comparisons', 'pre_purchase_order_item_selections.quotation_id', '=', 'quotation_comparisons.id')
                ->where('quotation_comparisons.supplier_id', $supplierId)
                ->exists();

            // Check if supplier is selected at pre-purchase order level
            $isSelectedGlobally = ($this->selectedQuotation() && $this->selectedQuotation()->supplier_id == $supplierId);

            return $isSelectedInItemSelections || $isSelectedGlobally;
        }

        return false;
    }
    /**
     * Get details that should be included in a purchase order for a specific supplier
     *
     * @param int $supplierId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDetailsForSupplier($supplierId)
    {
        // If we have item selections, get details based on those
        if ($this->itemSelections()->count() > 0) {
            $detailIds = $this->itemSelections()
                ->join('quotation_comparisons', 'pre_purchase_order_item_selections.quotation_id', '=', 'quotation_comparisons.id')
                ->where('quotation_comparisons.supplier_id', $supplierId)
                ->pluck('pre_purchase_order_item_selections.pre_purchase_order_detail_id');

            return $this->details()->whereIn('id', $detailIds)->get();
        }

        // If we don't have item selections but have a selected quotation for this supplier,
        // include all details
        $selectedQuotation = $this->selectedQuotation();
        if ($selectedQuotation && $selectedQuotation->supplier_id == $supplierId) {
            return $this->details;
        }

        return collect();
    }

}
