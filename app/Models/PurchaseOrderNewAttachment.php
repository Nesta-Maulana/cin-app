<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderNewAttachment extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relationship to the parent purchase order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderNew::class, 'purchase_order_id');
    }

    // Scope for active attachments
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for filtering by file type
    public function scopeByFileType($query, $fileType)
    {
        return $query->where('file_type', 'like', "%$fileType%");
    }
}
