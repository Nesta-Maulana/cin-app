<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApprovalRequest extends Model
{
    use HasFactory, LogsActivity;

    // Protect ID from mass assignment
    protected $guarded = ['id'];

    // Define constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('approval_requests')
            ->setDescriptionForEvent(fn(string $eventName) => "Approval Request {$eventName}");
    }

    /**
     * Filter scope
     *
     * @param $query
     * @param string|null $search
     * @return void
     */
    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('remarks', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
        });
    }

    /**
     * Relation to Approval
     */
    public function approval()
    {
        return $this->belongsTo(Approval::class, 'approval_id');
    }

    /**
     * Relation to Current Approval Level
     */
    public function currentLevel()
    {
        return $this->belongsTo(ApprovalLevel::class, 'current_level_id');
    }

    /**
     * Relation to Approval Logs
     */
    public function logs()
    {
        return $this->hasMany(ApprovalLog::class, 'approval_request_id');
    }

    /**
     * Check if the request is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is rejected
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the request is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Approve the approval request
     */
    public function approve($approverId, $remarks = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'current_level_id' => null,
            'remarks' => $remarks,
        ]);

        $this->logs()->create([
            'approval_request_id' => $this->id,
            'approval_level_id' => $this->current_level_id,
            'approver_id' => $approverId,
            'action' => self::STATUS_APPROVED,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Reject the approval request
     */
    public function reject($approverId, $remarks = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'current_level_id' => null,
            'remarks' => $remarks,
        ]);

        $this->logs()->create([
            'approval_request_id' => $this->id,
            'approval_level_id' => $this->current_level_id,
            'approver_id' => $approverId,
            'action' => self::STATUS_REJECTED,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Move to the next approval level
     */
    public function moveToNextLevel($nextLevelId)
    {
        $this->update([
            'current_level_id' => $nextLevelId,
        ]);
    }
    public function purchaseOrder()
    {
        return $this->morphTo(__FUNCTION__, 'class_name', 'reference_id')
            ->constrain([PurchaseOrder::class]);
    }

}
