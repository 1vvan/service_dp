<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role_id !== User::ROLE_MASTER) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        return $next($request);
    }
}
