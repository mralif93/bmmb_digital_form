<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_name' => $this->faker->company(),
            'ti_agent_code' => strtoupper($this->faker->unique()->bothify('??###')),
            'address' => $this->faker->address(),
            'email' => $this->faker->companyEmail(),
        ];
    }
}
