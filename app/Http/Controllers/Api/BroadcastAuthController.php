<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BroadcastAuthController extends Controller
{
    public function authorize(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $channelName = $request->input('channel_name');

        if (str_starts_with($channelName, 'private-restaurant.') && str_ends_with($channelName, '.kitchen')) {
            preg_match('/private-restaurant\.(\d+)\.kitchen/', $channelName, $matches);

            $restaurantId = (int) ($matches[1] ?? 0);

            if ($user->id === $restaurantId || ($user->user?->is_admin ?? false)) {
                return response()->json([
                    'auth' => config('broadcasting.connections.reverb.key').':'.$user->id,
                    'channel_data' => [
                        'user_id' => (string) $user->id,
                        'user_info' => [
                            'name' => $user->name,
                        ],
                    ],
                ]);
            }
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }
}
