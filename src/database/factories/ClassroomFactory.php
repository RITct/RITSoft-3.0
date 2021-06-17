<?php

namespace Database\Factories;

use App\Enums\Degrees;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Classroom::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "degree_type" => Degrees::BTECH,
            "semester" => 1,
            "department_code" => "CSE",
        ];
    }
}
