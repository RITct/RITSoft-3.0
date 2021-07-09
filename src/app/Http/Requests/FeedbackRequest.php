<?php

namespace App\Http\Requests;

use App\Models\Feedback;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $this->authUser = Auth::user();
        if ($this->route()->getName() == "feedbacks.show") {
            $this->feedback = Feedback::findOrFail($this->route()->parameter("feedback"));

            $canViewEverything = $this->authUser->faculty?->isPrincipal() || $this->authUser->isAdmin();

            $isHODAndSameDepartment = $this->authUser->faculty?->isHOD() &&
                $this->feedback->course->department_code == $this->authUser->faculty?->department_code;

            $isSameFaculty = $this->authUser->faculty_id == $this->feedback->faculty_id;

            return $canViewEverything || $isHODAndSameDepartment || $isSameFaculty;
        }
        return true;
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
