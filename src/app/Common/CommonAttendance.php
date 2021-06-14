<?php

namespace App\Common;

/**
 * Class CommonAttendance
 * @package App\Common
 *
 * Common utils for attendance controller
 */

class CommonAttendance
{
    public static function serializeCourse($data, $faculty = null, $is_admin = null): array
    {
        $new_data = [];
        foreach ($data as $period) {
            if (array_key_exists($period->course_id, $new_data)) {
                array_push($new_data[$period->course_id]["attendances"], $period);
            } else {
                $new_data[$period->course_id] = [
                    "id" => $period->course_id,
                    "faculty" => $period->course->faculty,
                    "subject" => $period->course->subject,
                    "semester" => $period->course->semester,
                    "attendances" => [$period],
                    "editable" => $faculty && $faculty->id == $period->course->faculty_id || $is_admin
                ];
            }
        }
        return $new_data;
    }

    public static function parseAttendanceInput($inp)
    {
        // This should be an excel/workbook parser
        // For now parse csv

        $data = explode(",", trim($inp));
        return array_filter($data, function ($val) {
            return $val != "";
        });
    }

    public static function serializeAttendanceFromStudent($attendance, $student_admission_id, $faculty, $is_admin)
    {
        foreach ($attendance as $period) {
            $absent = false;
            foreach ($period->absentees as $absentee) {
                if ($student_admission_id == $absentee->student_admission_id) {
                    $absent = true;
                }
                $period->medical_leave = $absentee->medical_leave;
                $period->duty_leave = $absentee->duty_leave;
            }
            $period->absent = $absent;
            if ($faculty && $period->course->faculty_id == $faculty->id || $is_admin) {
                // Only allow that particular faculty or admin to edit
                $period->editable = true;
            } else {
                $period->editable = false;
            }
        }
    }
}
