<?php

namespace Database\Factories;

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
            "admission_id" => $this->faker->unique(),
            "name" => Str::random(10),
            "address" => Str::random(20),
            "phone" => $this->faker->unique(),
            "roll_no" => $this->faker->unique(),
        ];
    }
}
