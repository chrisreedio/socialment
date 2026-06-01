<?php

namespace ChrisReedIO\Socialment\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

use function response;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected function respondNotYetImplemented(): JsonResponse
    {
        return response()->json([
            'message' => 'Not yet implemented',
        ], 501);
    }
}
