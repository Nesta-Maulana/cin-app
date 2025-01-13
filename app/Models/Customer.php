<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
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

    public function getStatusAttribute()
    {
        if ($this->is_active == 0) {
            return '<span class="badge rounded-pill badge-light-secondary">Inactive</span>';
        } else {
            return '<span class="badge rounded-pill badge-light-success">Active</span>';
        }
    }
    public function files()
    {
        return $this->morphMany(File::class, 'reference', 'class_name', 'reference_id');
    }
    public function approvalRequest($event)
    {
        return $this->morphOne(ApprovalRequest::class, 'reference', 'class_name', 'reference_id')
            ->whereHas('approval', function ($query) use ($event) {
                $query->where('event', $event);
            })->where('status', 'pending');
    }

    public function customerOrders()
    {
        return $this->hasMany(CustomerOrder::class);
    }


}
