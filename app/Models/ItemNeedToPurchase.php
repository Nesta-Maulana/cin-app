<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class ItemNeedToPurchase extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];

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
    public function scopePurchasingRole($query)
    {
        return $query->where('process_status', 'Waiting Process Purchasing');
    }
    public function itemNeedToPurchaseDetail()
    {
        return $this->hasMany(ItemNeedToPurchaseDetail::class, 'header_id', 'id');
    }


    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }

}
