<?php

use ChrisReedIO\Socialment\Tests\Models\User;
use ChrisReedIO\Socialment\Tests\TestCase;

uses(TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in(__DIR__)
    ->beforeEach(function () {
        config(['socialment.models.user' => User::class]);
    });
