<?php

namespace Database\Seeders;

use App\Enums\Degrees;
use App\Models\Classroom;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classroomS2CSE = Classroom::create([
            "semester" => 2,
            "degree_type" => Degrees::BTECH,
            "department_code" => "CSE",
        ]);

        Classroom::create([
            "semester" => 1,
            "degree_type" => Degrees::BTECH,
            "department_code" => "CSE",
            "promotion_id" => $classroomS2CSE->id
        ]);
    }
}
