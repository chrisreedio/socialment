<?php

use Illuminate\Database\Eloquent\Model;

arch('debug')
    ->expect(['dd', 'dump', 'env', 'ray'])
    ->each->not->toBeUsed();

arch('app')
    ->expect('App')
    ->toUseStrictTypes();

arch('http actions')
    ->expect('App\Http')
    ->toOnlyBeUsedIn('App\Http');

arch('controllers')
    ->expect('ChrisReedIO\Socialment\Http\Controllers')
    ->toHaveSuffix('Controller');

arch('requests')
    ->expect('ChrisReedIO\Socialment\Http\Requests')
    ->toHaveSuffix('Request');

arch('resources')
    ->expect('ChrisReedIO\Socialment\Http\Resources')
    ->toHaveSuffix('Resource');

arch('models extending')
    ->expect('ChrisReedIO\Socialment\Models')
    ->toExtend(Model::class);

arch('facades')
    ->expect('Illuminate\Support\Facades')
    ->not->toBeUsed()
    ->ignoring([
        \ChrisReedIO\Socialment\SocialmentServiceProvider::class,
        \ChrisReedIO\Socialment\SocialmentPlugin::class,
        \ChrisReedIO\Socialment\Facades\Socialment::class,
    ]);
