<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagerAdminOrOwnClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Неавторизований'], 401);
        }

        // Manager and admin always have access
        if ($user->role_id === User::ROLE_MANAGER || $user->role_id === User::ROLE_ADMIN) {
            return $next($request);
        }

        // Client can only access their own data
        if ($user->role_id === User::ROLE_CLIENT) {
            $clientRouteParam = $request->route('client');
            $clientId = is_object($clientRouteParam) ? $clientRouteParam->id : (int) $clientRouteParam;

            if ($user->client_id && $user->client_id === $clientId) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Доступ заборонено'], 403);
    }
}
