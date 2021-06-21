<?php

namespace App\Http\Middleware;

use App\Models\Faculty;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SameFaculty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $auth_user = Auth::user();
        if ($request->route()->parameter("faculty") != $auth_user->faculty_id && !$auth_user->isAdmin()) {
            abort(403);
        }
        return $next($request);
    }
}
