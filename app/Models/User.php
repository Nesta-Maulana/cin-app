<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set the user's password hash.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('username', 'ilike', "%$search%")
                    ->orWhere('email', 'ilike', "%$search%")
                    ->orWhere('name', 'ilike', "%$search%");
            });
        });
    }

    public function scopeAdministrator($query)
    {
        return $query->whereHas('roles');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['username', 'name', 'email', 'is_active']);
    }

    public function userActivities()
    {
        return $this->hasMany(ActivityLog::class, 'causer_id', 'id');
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_department', 'user_id', 'department_id')
            ->withTimestamps();
    }
}
