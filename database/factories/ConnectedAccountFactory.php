<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Database\Factories;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectedAccountFactory extends Factory
{
    protected $model = ConnectedAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => $this->faker->word(),
            'provider_user_id' => $this->faker->randomNumber(5),
            'name' => $this->faker->firstName(),
            'nickname' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'token' => $this->faker->text(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
