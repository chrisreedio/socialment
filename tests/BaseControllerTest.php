<?php

use ChrisReedIO\Socialment\Http\Controllers\BaseController;

it('responds with 501 from respondNotYetImplemented', function () {
    $controller = new class extends BaseController
    {
        public function __invoke()
        {
            return $this->respondNotYetImplemented();
        }
    };

    $response = $controller();

    expect($response->getStatusCode())->toBe(501)
        ->and($response->getData(true))->toBe(['message' => 'Not yet implemented']);
});
