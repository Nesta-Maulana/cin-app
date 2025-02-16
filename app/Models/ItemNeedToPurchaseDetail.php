<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ItemNeedToPurchaseDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function itemRequestDetail()
    {
        return $this->belongsTo(ItemRequestDetail::class);
    }
    public function itemNeedToPurchaseHeader(): BelongsTo
    {
        return $this->belongsTo(ItemNeedToPurchase::class, 'header_id');
    }

}

