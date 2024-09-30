<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ChatRoom extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function chatDetail()
    {
        return $this->hasMany(ChatRoomDetail::class, 'chat_room_id', 'id')->orderBy('created_at', 'desc');
    }

    public function scopeFilterByBot($query, $bot_id)
    {
        return $query->whereHas('contact', function ($q) use ($bot_id) {
            $q->where('bot_id', $bot_id);
        });
    }
    public function scopeHasChatDetail($query)
    {
        return $query->whereHas('chatDetail');
    }
}
