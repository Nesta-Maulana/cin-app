<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Approval extends Model
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
     * Relasi ke ApprovalLevel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvalLevels()
    {
        return $this->hasMany(ApprovalLevel::class);
    }

    /**
     * Scope untuk hanya menampilkan approval yang aktif.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
