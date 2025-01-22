<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApprovalLevel extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        });
    }


    /**
     * Relasi ke Approval.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    /**
     * Scope untuk hanya menampilkan level approval yang diperlukan.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequired($query)
    {
        return $query->where('required', true);
    }

    /**
     * Ambil entitas approver (user/role) berdasarkan class_name_approver_type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function approver()
    {
        return $this->morphTo(__FUNCTION__, 'class_name_approver_type', 'approver_reference_id');
    }
    public function getStatusAttribute()
    {
        if ($this->is_active == 0) {
            return '<span class="badge rounded-pill badge-light-secondary">Inactive</span>';
        } else {
            return '<span class="badge rounded-pill badge-light-success">Active</span>';
        }
    }

}
