<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class HODUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'CSE HOD',
            'email' => 'csehod@ritsoft.com',
            'password' => bcrypt('123456')
        ]);

        $user->assignRole('HOD');
    }
}
