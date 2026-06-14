<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        abort_unless($user->role === 'admin' || (method_exists($user, 'accountType') && $user->accountType() === 'admin'), 403);

        return $next($request);
    }
}
