<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Tests\database\factories;

use ChrisReedIO\Socialment\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
