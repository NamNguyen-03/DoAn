<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Roles;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo các role
        $superadminRole = Roles::create(['role_name' => 'superadmin']);
        $adminRole = Roles::create(['role_name' => 'admin']);

        // Tạo admin đầu tiên và gán role superadmin
        $superadmin = Admin::create([
            'admin_name' => 'Super Admin',
            'admin_phone' => '0921388888',
            'admin_email' => 'superadmin@example.com',
            'admin_password' => Hash::make('password'),
        ]);
        $superadmin->roles()->attach($superadminRole);

        // Tạo admin bậc 2 và gán role admin
        $admin = Admin::create([
            'admin_name' => 'Admin User',
            'admin_email' => 'admin@example.com',
            'admin_phone' => '0921313233',
            'admin_password' => Hash::make('password'),
        ]);
        $admin->roles()->attach($adminRole);
    }
}
