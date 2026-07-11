<?php

use Illuminate\Database\Eloquent\Model;

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'env', 'ray'])
    ->each->not->toBeUsed();

arch('app')
    ->expect('App')
    ->toUseStrictTypes();

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
