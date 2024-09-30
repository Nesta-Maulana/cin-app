<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    protected $casts = [
        'data' => 'array', // Automatically casts the 'data' column to an array
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function scopeByPhoneNumber($query, $phone_number)
    {
        return $query->whereRaw("CONCAT(country_code, contact_number) = ?", [$phone_number]);
    }

    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class, 'contact_id', 'id');
    }
}
