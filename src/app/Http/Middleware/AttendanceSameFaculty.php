<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSameFaculty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $auth_user = Auth::user();

        $attendance = Attendance::with("course.faculty")
            ->findOrFail($request->route()->parameter("attendance"));

        if($attendance->course->faculty != $auth_user->faculty && !$auth_user->is_admin())
            abort("403");

        return $next($request);
    }
}
