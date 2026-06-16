<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@medibook.com',
            'password' => bcrypt('password'),
        ]);
    }
}