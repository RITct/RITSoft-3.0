<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $currentRoute = $this->route()->getName();
        $this->authUser = Auth::user();
        $attendanceObjectRoutes = ["attendance.edit", "attendance.update", "attendance.destroy"];

        if ($currentRoute == "attendance.show") {
            $student_admission_id = $this->route()->parameter("attendance");
            $isSameStudent = $this->authUser->student_admission_id == $student_admission_id;

            // Permit either faculty or same student or admin
            return $isSameStudent || $this->authUser->faculty_id != null || $this->authUser->isAdmin();
        } elseif (in_array($currentRoute, $attendanceObjectRoutes)) {
            $this->attendance = Attendance::getBaseQuery()
                ->findOrFail($this->route()->parameter("attendance"));

            return $this->attendance->course->hasFaculty($this->authUser->faculty_id) || $this->authUser->isAdmin();
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if (in_array($this->route()->getName(), ["attendance.index", "attendance.show"])) {
            return [
                "from" => ["date"],
                "to" => ["date"],
            ];
        }
        if ($this->route()->getName() == "attendance.store") {
            return [
                "date" => ["required", "date"],
                "course_id" => ["required", "integer"],
                "hour" => ["required", "integer"],
            ];
        }
        return [];
    }
}
