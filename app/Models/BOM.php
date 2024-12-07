<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class BOM extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'boms';
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function getStatusNameAttribute()
    {
        $statuses = [
            0 => 'Draft',
            1 => 'Requested to Warehouse',
            2 => 'Half Fulfillment Warehouse',
            3 => 'Waiting to Warehouse Shipment'
        ];

        return $statuses[$this->status] ?? 'Unknown Status';
    }

    public function bomDetails()
    {
        return $this->hasMany(BomDetail::class, 'bom_id', 'id');
    }
}
