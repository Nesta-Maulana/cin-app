<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function materialPrice()
    {
        return $this->hasOne(MaterialPrice::class)->latestOfMany('effective_date');
    }
}
