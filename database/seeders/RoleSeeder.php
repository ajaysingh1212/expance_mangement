<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $admin = Role::create(['name'=>'Admin','slug'=>'admin']);
        $user = Role::create(['name'=>'User','slug'=>'user']);

        $permissions = Permission::all();
        $admin->permissions()->sync($permissions->pluck('id'));
    }
}
