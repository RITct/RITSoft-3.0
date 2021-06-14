<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSameStudent
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

        // /attendance/<student_admission_id>
        if ($auth_user->student && $auth_user->student->admission_id != $request->route()->parameter("attendance")) {
            abort(403);
        }
        return $next($request);
    }
}
