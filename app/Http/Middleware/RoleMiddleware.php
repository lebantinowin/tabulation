<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            // Redirect to the correct login portal based on required role
            if ($role === 'superadmin') {
                return redirect()->route('superadmin.login');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.login');
            }
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        if ($role === 'admin' && $userRole === 'superadmin') {
            // Superadmins can do everything an admin can do
        } elseif ($userRole !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
