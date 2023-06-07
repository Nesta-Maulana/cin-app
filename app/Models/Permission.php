<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as PermissionSpatie;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends PermissionSpatie
{
    use HasFactory;

    public function scopeFilter($query, $search) 
    {
        $query->when($search ?? false, function($query, $search){
            return $query->where('name', 'like', "%$search%");
        });
    }
}
