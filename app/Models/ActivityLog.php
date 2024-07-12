<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log'; // Sesuaikan dengan nama tabel Anda

    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id', 'id')->withDefault(['username' => '-']);
    }
}
