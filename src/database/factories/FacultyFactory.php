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
            "id" => $this->faker->unique()->realText(20),
            "name" => $this->faker->name,
            "address" => $this->faker->realText(),
            "phone" => $this->faker->unique()->numerify("##########"),
            "department_code" => "CSE",
            "photo_url" => $this->faker->url
        ];
    }
}
