<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class DeliveryOrder extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }


    /**
     * Get the customer order associated with the delivery order.
     */
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    /**
     * Get the delivery order details for this delivery order.
     */
    public function details()
    {
        return $this->hasMany(DeliveryOrderDetail::class, 'header_id');
    }

    /**
     * Get the user who created this delivery order.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this delivery order.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
