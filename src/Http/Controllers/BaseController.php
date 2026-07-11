<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

use function response;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected function respondNotYetImplemented(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Not yet implemented',
        ], 501);
    }
}
