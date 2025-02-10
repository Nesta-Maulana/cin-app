<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ItemRequestDetail extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean', // Boolean untuk status
        'additional' => 'array', // JSON sebagai array
    ];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        });
    }
    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }


    /**
     * Relationship: Item / 关系：项目
     */
    /**
     * Relationship: Item Price History / 关系：项目价格历史
     */
    public function itemPriceHistory()
    {
        return $this->belongsTo(ItemPriceHistory::class, 'item_price_history_id');
    }
    /**
     * Relationship: Delivery Order Details / 关系：送货单明细
     */
    public function deliveryOrderDetails()
    {
        return $this->hasMany(DeliveryOrderDetail::class, 'item_request_detail_id');
    }

}
