<?php

namespace App\Common;

use Illuminate\Support\Collection;

/**
 * Class CommonFaculty
 * @package App\Common
 *
 * Common utils for attendance controller
 */

class CommonFaculty
{
    public static function markEditableFaculty($faculty, $auth_user) {
        $faculty->editable = $auth_user->faculty_id == $faculty->id || $auth_user->isAdmin();
        if(!$auth_user->isAdmin()) {
            $is_same_department = $auth_user->faculty->department_code == $faculty->department_code;
            $is_same_faculty = $auth_user->faculty_id == $faculty->id;
            $faculty->deletable = $auth_user->faculty->isHOD() && $is_same_department && !$is_same_faculty;
        }
        else {
            $faculty->deletable = true;
        }

        return $faculty;
    }

    public static function serializeFacultyByDepartment($faculties, $auth_user, $department_code = null): array
    {
        $result = [];
        foreach ($faculties as $faculty) {
            if ($department_code && $faculty->department_code == $department_code || !$department_code) {
                if (key_exists($faculty->department_code, $result)) {
                    array_push($result[$faculty->department_code],
                        CommonFaculty::markEditableFaculty($faculty, $auth_user));
                } else {
                    $result[$faculty->department_code] = [CommonFaculty::markEditableFaculty($faculty, $auth_user)];
                }
            }
        }

        return $result;
    }
}
