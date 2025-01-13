<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApprovalLog extends Model
{
    use HasFactory, LogsActivity;

    // Properti yang boleh diisi (mass assignment)
    protected $fillable = [
        'approval_request_id',
        'approval_level_id',
        'approver_id',
        'action',
        'remarks',
    ];

    /**
     * Konfigurasi logging aktivitas dengan spatie/laravel-activitylog.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Log semua perubahan atribut
            ->useLogName('approval_log') // Nama log
            ->setDescriptionForEvent(fn(string $eventName) => "Approval log has been {$eventName}"); // Deskripsi
    }

    /**
     * Relasi ke model ApprovalRequest.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvalRequest()
    {
        return $this->belongsTo(ApprovalRequest::class);
    }

    /**
     * Relasi ke model ApprovalLevel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvalLevel()
    {
        return $this->belongsTo(ApprovalLevel::class);
    }

    /**
     * Relasi ke model User sebagai approver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Scope untuk filter berdasarkan action.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByAction($query, $action)
    {
        return $query->when($action, function ($query, $action) {
            $query->where('action', $action);
        });
    }
}
