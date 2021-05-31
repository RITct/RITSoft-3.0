<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'name' => 'Computer Science And Engineering',
            'code' => 'CSE'
        ]);

        Department::create([
            'name' => 'Electronics And Communication Engineering',
            'code' => 'ECE'
        ]);

    }
}
