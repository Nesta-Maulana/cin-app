<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CustomerOrder extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    protected $casts = [
        'order_date' => 'date', // Cast order_date to a Date instance
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
    public function scopeRequestStatus($query, $request_status = [])
    {
        return $query->whereHas('itemRequests', function ($q) use ($request_status) {
            $q->whereIn('request_status', $request_status);
        });
    }

    public function getStatusAttribute()
    {
        if ($this->is_active == 0) {
            return '<span class="badge rounded-pill badge-light-secondary">Inactive</span>';
        } else {
            return '<span class="badge rounded-pill badge-light-success">Active</span>';
        }
    }
    public function getTotalItemNeedToProcessAttribute()
    {
        return $this->itemRequests()
            ->whereIn('request_status', ['Waiting On Process Warehouse','Partial Delivery by Warehouse'])
            ->count();
    }
    public function files()
    {
        return $this->morphMany(File::class, 'reference', 'class_name', 'reference_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function personInCharge()
    {
        return $this->belongsTo(User::class);
    }
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }
    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class);
    }
    public function itemNeedToPurchases()
    {
        return $this->hasMany(ItemNeedToPurchase::class);
    }

}
