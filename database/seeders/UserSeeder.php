<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::create([
            'name'=>'Super Admin',
            'email'=>'admin@gmail.com',
            'password'=>bcrypt('12345678'),
            'is_super_admin'=>1
        ]);

        $role = Role::where('slug','admin')->first();
        $user->roles()->attach($role->id);
    }
}
