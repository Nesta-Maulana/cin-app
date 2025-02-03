<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class ItemRequest extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    protected $casts = [
        'order_date' => 'date', // Cast order_date to a Date instance
        'request_date' => 'date', // Cast order_date to a Date instance
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

    public function getStatusAttribute()
    {
        if ($this->is_active == 0) {
            return '<span class="badge rounded-pill badge-light-secondary">Inactive</span>';
        } else {
            return '<span class="badge rounded-pill badge-light-success">Active</span>';
        }
    }
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    /**
     * Relationship: Item Request Details / 关系：项目请求详情
     */
    public function details()
    {
        return $this->hasMany(ItemRequestDetail::class, 'item_request_id');
    }
    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function scopePerDepartment($query)
    {
        $userDepartments = auth()->user()->departments->pluck('id')->toArray();

        return $query->whereHas('createdBy.departments', function ($q) use ($userDepartments) {
            $q->whereIn('departments.id', $userDepartments);
        });
    }

}
