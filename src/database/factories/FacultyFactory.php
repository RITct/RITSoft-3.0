<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FacultyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Faculty::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => User::factory(),
            "name" => Str::random(10),
            "address" => Str::random(10),
            "phone" => $this->faker->unique(),
            "department_code" => "CSE"
        ];
    }
}
