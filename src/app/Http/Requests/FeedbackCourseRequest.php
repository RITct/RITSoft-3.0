<?php

namespace App\Http\Requests;

use App\Models\Course;
use App\Models\Curriculum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FeedbackCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $this->authUser = Auth::user();
        $courseId = $this->route()->parameter("course_id");
        $this->course = Course::findOrFail($courseId);
        $this->courseCurriculum = Curriculum::where([
            "student_admission_id" => $this->authUser->student_admission_id,
            "course_id" => $courseId
        ])->first();

        if ($this->route()->getName() == "faculties.courses.index") {
            return true;
        }

        return $this->authUser->student->hasCourse($courseId)
            && !$this->courseCurriculum->is_feedback_completed
            && $this->course->is_feedback_open;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
