<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $cse = Department::where('code', 'CSE')->first();
        Subject::create([
           'name' => 'Data Structures',
           'code' => 'CSE201',
           'credits' => 4,
           'type' => 'test',
           'department_code' => $cse->code
        ]);
    }
}
