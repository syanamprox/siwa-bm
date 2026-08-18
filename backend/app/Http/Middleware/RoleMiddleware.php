<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    protected $allowedRoles;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles = null): Response
    {
        $this->allowedRoles = is_array($roles) ? $roles : func_get_args()[2] ?? [];

        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login.',
                    'code' => 401
                ], 401);
            }
            return redirect('/login');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // Admin dapat mengakses semua
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Check if user role is allowed
        if (!in_array($userRole, $this->allowedRoles)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Access denied for role: ' . $userRole . '. '. implode(', ', $this->allowedRoles),
                    'code' => 403
                ], 403);
            }

            return redirect('/dashboard')->with('error', 'Unauthorized. Access denied for your role.');
        }

        return $next($request);
    }
}