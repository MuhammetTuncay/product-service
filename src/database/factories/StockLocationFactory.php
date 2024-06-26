<?php
namespace Database\Factories;

use App\Models\StockLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockLocationFactory extends Factory
{
    protected $model = StockLocation::class;

    public function definition(): array
    {
        return [
            'location_name' => $this->faker->city,
        ];
    }
}
