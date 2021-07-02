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
    public function handle(Request $request, Closure $next)
    {
        $authUser = Auth::user();

        $attendance = Attendance::with("course.faculties")
            ->findOrFail($request->route()->parameter("attendance"));

        if (!$attendance->course->hasFaculty($authUser->faculty_id) && !$authUser->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
