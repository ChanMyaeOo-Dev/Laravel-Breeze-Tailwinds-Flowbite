<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastAuthController extends Controller
{
    public function authorize(Request $request): JsonResponse
    {
        $response = Broadcast::auth($request);

        return response()->json($response);
    }
}
