<?php

namespace Database\Factories;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wishlist>
 */
class WishlistFactory extends Factory
{
    protected $model = Wishlist::class;

    public function definition(): array
    {
        return [
            'prioritet' => $this->faker->numberBetween(1, 10),  // Genererer et tilfeldig tall mellom 1 og 10
            'hva' => $this->faker->word(),  // Genererer et tilfeldig ord
            'koster' => $this->faker->numberBetween(100, 10000),  // Genererer et tilfeldig beløp mellom 100 og 10000
            'url' => $this->faker->url(),  // Genererer en tilfeldig URL
            'antall' => $this->faker->numberBetween(1, 5),  // Genererer et tilfeldig antall mellom 1 og 5
            'status' => $this->faker->randomElement(['pending', 'completed', 'in-progress']),  // Tilfeldig status
            'deleted_at' => null,  // Soft delete er satt til null som standard
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
