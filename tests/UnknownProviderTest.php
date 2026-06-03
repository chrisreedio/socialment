<?php

use function Pest\Laravel\get;

beforeEach(function () {
    config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
});

it('returns 404 when redirecting to an unregistered provider', function () {
    get(route('socialment.redirect', ['provider' => 'unknown']))
        ->assertNotFound();
});

it('returns 404 on the panel redirect for an unregistered provider', function () {
    get(route('socialment.redirect.panel', ['provider' => 'unknown', 'panelId' => 'admin']))
        ->assertNotFound();
});
