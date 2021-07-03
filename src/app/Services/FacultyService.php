<?php

namespace App\Services;

use App\Exceptions\IntendedException;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FacultyService
{
    /**
     * Get Faculties based on authenticated user
     * @param $authUser
     */
    public function getFacultiesAccordingToAuthUser($authUser)
    {
        $faculties = Faculty::with("courses", "advisorClassroom");

        // Explicitly given, cause technically an HOD, Dean, Principal can be the same person
        // Although highly unlikely
        if ($authUser->isAdmin() || $authUser->faculty->isPrincipal()) {
        } elseif ($authUser->faculty->isHod()) {
            $faculties = $faculties->where("department_code", $authUser->faculty->department_code);
        }

        return $faculties->get();
    }

    public function markEditableFaculty($faculty, $auth_user)
    {
        $faculty->editable = $auth_user->faculty_id == $faculty->id || $auth_user->isAdmin();
        if (!$auth_user->isAdmin()) {
            $is_same_department = $auth_user->faculty->department_code == $faculty->department_code;
            $is_same_faculty = $auth_user->faculty_id == $faculty->id;
            $faculty->deletable = $auth_user->faculty->isHOD() && $is_same_department && !$is_same_faculty;
        } else {
            $faculty->deletable = true;
        }

        return $faculty;
    }

    public function serializeFacultyByDepartment($faculties, $auth_user, $department_code = null): array
    {
        $result = [];
        foreach ($faculties as $faculty) {
            if ($department_code && $faculty->department_code == $department_code || !$department_code) {
                if (key_exists($faculty->department_code, $result)) {
                    array_push(
                        $result[$faculty->department_code],
                        $this->markEditableFaculty($faculty, $auth_user)
                    );
                } else {
                    $result[$faculty->department_code] = [$this->markEditableFaculty($faculty, $auth_user)];
                }
            }
        }

        return $result;
    }

    public function createFaculty($data, $authUser)
    {
        $user = User::create([
            "username" => $data["id"],
            "password" => $data["id"],
            "email" => $data["email"],
        ]);
        $faculty = new Faculty([
            "name" => $data["name"],
            "phone" => $data["phone"],
        ]);
        $faculty->user_id = $user->id;
        $faculty->id = $data["id"];

        if ($authUser->isAdmin()) {
            if (!array_key_exists("department_code", $data)) {
                throw new IntendedException("Department Code Is Missing");
            }
            $department_code = $data["department_code"];
            $faculty->department_code = $department_code;
        } else {
            $faculty->department_code = $authUser->faculty->department_code;
        }
        $faculty->save();

        $user->faculty()->associate($faculty);
    }

    public function updateFaculty($faculty, $name, $phone, $email)
    {
        $faculty_update_array = [
            "name" => $name,
            "phone" => $phone
        ];
        $faculty->update(array_filter($faculty_update_array));

        if ($email) {
            $faculty->user->update(["email" => $email]);
        }
    }
}
