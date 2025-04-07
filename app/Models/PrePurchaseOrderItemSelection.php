<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PrePurchaseOrderItemSelection extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    /**
     * Configure activity log options
     * 配置活动日志选项
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('pre_purchase_order_item_selection')
            ->setDescriptionForEvent(fn(string $eventName) => "Item supplier selection has been {$eventName} / 物品供应商选择已被{$eventName}");
    }

    /**
     * Get the pre-purchase order that owns the selection
     * 获取拥有此选择的预采购单
     */
    public function prePurchaseOrder()
    {
        return $this->belongsTo(PrePurchaseOrder::class);
    }

    /**
     * Get the quotation that was selected
     * 获取被选择的报价
     */
    public function quotation()
    {
        return $this->belongsTo(QuotationComparison::class, 'quotation_id');
    }
}
