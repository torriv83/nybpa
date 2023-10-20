<?php

namespace Database\Factories;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimesheetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Timesheet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'fra_dato' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            'til_dato' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            'description' => $this->faker->sentence(),
            'totalt' => sprintf('%02d:%02d', $this->faker->numberBetween(0, 23), $this->faker->numberBetween(0, 59)),
            'user_id' => User::factory(),
            'unavailable' => $this->faker->boolean(),
            'allDay' => $this->faker->boolean(),
        ];
    }
}
