<?php

namespace App\Http\Requests;

use App\Models\Faculty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FacultyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $currentRoute = $this->route()->getName();
        $this->authUser = Auth::user();
        $objectRoutes = ["faculty.show", "faculty.destroy", "faculty.edit", "faculty.update"];

        if (in_array($currentRoute, $objectRoutes)) {
            $facultyId = $this->route()->parameter("faculty");
            $this->faculty = Faculty::with("user")->findOrFail($facultyId);

            if ($currentRoute == "faculty.show") {
                $hasUnrestrictedAccess = $this->authUser->isAdmin() || $this->authUser->faculty->isPrincipal();

                $isDepartmentHOD = $this->authUser->faculty?->isHOD()
                    && $this->faculty->department_code == $this->authUser->faculty->department_code;

                $isSameFaculty = $facultyId == $this->authUser->faculty_id;

                return $isSameFaculty || $isDepartmentHOD || $hasUnrestrictedAccess;
            } elseif ($currentRoute == "faculty.destroy") {
                $isHODSameDepartment = $this->faculty->department_code == $this->authUser->faculty?->department_code;

                $sameFaculty = $this->authUser->faculty_id == $facultyId;

                return $this->authUser->faculty?->isHOD() && $isHODSameDepartment && !$sameFaculty
                    || $this->authUser->isAdmin();
            } elseif ($currentRoute == "faculty.edit" || $currentRoute == "faculty.update") {
                return $this->faculty->id == $this->authUser->faculty_id || $this->authUser->isAdmin();
            }
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

        if ($this->route()->getName() == "faculty.store") {
            return [
                "id" => "required|unique:faculties,id",
                "name" => "required",
                "phone" => "required|numeric|digits:10|unique:faculties,phone",
                "email" => "required|email|unique:users,email",
                "department_code" => "exists:departments,code"
            ];
        }

        if ($this->route()->getName() == "faculty.update") {
            return [
                "phone" => "numeric|digits:10",
                "email" => "email|unique:users,email"
            ];
        }
        return [];
    }
}
