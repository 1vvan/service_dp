<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role_id !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        return $next($request);
    }
}
