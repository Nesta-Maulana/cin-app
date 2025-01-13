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


}
