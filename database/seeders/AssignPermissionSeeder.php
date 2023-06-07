<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allPermission = Permission::pluck('name')->toArray();
        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo($allPermission);
    }
}
