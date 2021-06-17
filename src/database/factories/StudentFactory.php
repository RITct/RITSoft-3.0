<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "admission_id" => $this->faker->unique()->realText(13),
            "classroom_id" => Classroom::all()->random()->id,
            "name" => Str::random(10),
            "address" => Str::random(20),
            "phone" => $this->faker->unique()->realText(10),
            "roll_no" => $this->faker->unique()->numberBetween(1),
        ];
    }
}
