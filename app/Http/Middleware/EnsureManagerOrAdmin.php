<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagerOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $roleId = $request->user()?->role_id;

        if ($roleId !== User::ROLE_MANAGER && $roleId !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        return $next($request);
    }
}
