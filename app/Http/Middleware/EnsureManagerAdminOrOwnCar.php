<?php

namespace App\Http\Middleware;

use App\Models\ClientCar;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagerAdminOrOwnCar
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Неавторизований'], 401);
        }

        if ($user->role_id === User::ROLE_MANAGER || $user->role_id === User::ROLE_ADMIN) {
            return $next($request);
        }

        if ($user->role_id === User::ROLE_CLIENT) {
            $carParam = $request->route('car');
            $carClientId = is_object($carParam)
                ? $carParam->client_id
                : ClientCar::find($carParam)?->client_id;

            if ($user->client_id && $user->client_id === $carClientId) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Доступ заборонено'], 403);
    }
}
