<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LurahMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
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
        $allowedRoles = ['admin', 'lurah'];

        if (!in_array($user->role, $allowedRoles)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Lurah access required.',
                    'code' => 403
                ], 403);
            }
            return redirect('/dashboard')->with('error', 'Unauthorized. Lurah access required.');
        }

        return $next($request);
    }
}
