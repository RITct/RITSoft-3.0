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
    public static function serializeFacultyByDepartment($faculties, $department_code = null): array
    {
        $result = [];
        foreach ($faculties as $faculty) {
            if ($department_code && $faculty->department_code == $department_code || !$department_code) {
                if (key_exists($faculty->department_code, $result)) {
                    array_push($result[$faculty->department_code], $faculty);
                } else {
                    $result[$faculty->department_code] = [$faculty];
                }
            }
        }

        return $result;
    }
}
