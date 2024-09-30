<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Question extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearMenuCache();
        });

        static::deleted(function () {
            static::clearMenuCache();
        });
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

}
