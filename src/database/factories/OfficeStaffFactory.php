<?php

namespace Database\Factories;

use App\Models\OfficeStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeStaffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfficeStaff::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "phone" => $this->faker->numerify("##########"),
            "address" => $this->faker->realText(30)
        ];
    }
}
