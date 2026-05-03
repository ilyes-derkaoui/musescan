<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $adminEmail = (string) config('app.admin_email', 'admin@museum.local');

        if (! $user || strtolower((string) $user->email) !== strtolower($adminEmail)) {
            abort(403, 'Accès réservé à l\'administration.');
        }

        return $next($request);
    }
}
