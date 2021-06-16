<?php

namespace Database\Factories;

use App\Enums\CourseTypes;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "type" => CourseTypes::REGULAR,
            "semester" => 1
        ];
    }
}
