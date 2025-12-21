<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'role' => 'cfe',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a CFE user.
     */
    public function cfe(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'cfe',
        ]);
    }

    /**
     * Create a Branch Manager user.
     */
    public function branchManager(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'branch_manager',
        ]);
    }

    /**
     * Create a HQ user.
     */
    public function hq(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'headquarters',
        ]);
    }

    /**
     * Create an Admin user.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
