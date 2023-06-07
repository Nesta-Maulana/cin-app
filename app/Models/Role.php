<?php

namespace App\Models;

use Spatie\Permission\Models\Role as RoleSpatie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends RoleSpatie
{
    use HasFactory;

    public function scopeFilter($query, $search) 
    {
        $query->when($search ?? false, function($query, $search){
            return $query->where('name', 'like', "%$search%");
        });
    }
}
