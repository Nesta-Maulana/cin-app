<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearSettingCache();
        });

        static::deleted(function () {
            static::clearSettingCache();
        });
    }

    public static function clearSettingCache()
    {
        Cache::flush();
    }
}
