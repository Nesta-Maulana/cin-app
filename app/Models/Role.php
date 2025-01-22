<?php

namespace App\Models;

use Spatie\Permission\Models\Role as RoleSpatie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends RoleSpatie
{
    use HasFactory, LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function scopeFilter($query, $search)
    {
        $query->when($search ?? false, function($query, $search){
            return $query->where('name', 'like', "%$search%");
        });
    }
    public function scopeAdministrator($query)
    {
        return $query->whereIn('name',['Administrator']);
    }
    public function scopeExceptSuperAdmin($query)
    {
        return $query->where('name', '!=', 'Super Admin');
    }
}
