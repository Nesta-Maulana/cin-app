<?php

namespace App\Models;

use App\Services\BotService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class Bot extends Model
{
    use HasFactory, HasRoles, LogsActivity;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function tenant()
    {
        return $this->hasOne(User::class, 'id', 'tenant_id')->withDefault(['tenant_name' => 'Central Panel']);
    }
    public function getStatusBotAttribute()
    {
        $botService = new BotService();
        $getHostDevice = $botService->getHostDevice($this->session_name, $this->token, $this->phone_number, $this->id);
        if ($getHostDevice['status'] >= 200 && $getHostDevice['status'] < 300) {
            if (isset($getHostDevice['data'])) {
                return [
                    'status' => 'Connected',
                    'message' => 'Connected With Phone Number : ' . trim($getHostDevice['data']['response']['phoneNumber'], '@c.us')
                ];
            }

        }

        return [
            'status' => 'Disconnected',
            'message' => $getHostDevice['message']
        ];

    }
}
