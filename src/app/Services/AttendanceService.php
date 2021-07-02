<?php

namespace App\Services;

use App\Enums\CourseTypes;
use App\Exceptions\IntendedException;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Student;
use DateTime;
use Exception;

class AttendanceService
{
    /**
     * Get Attendance based on authenticated user
     * @param string|null $from
     * @param string|null $to
     * @param $auth_user
     * @return mixed
     */
    public function getAllAttendance(?string $from, ?string $to, $auth_user)
    {
        $faculty = $auth_user->faculty;

        if ($auth_user->isAdmin() || $faculty?->isPrincipal()) {
            // OR Principal
            $query = Attendance::getBaseQuery($from, $to);
        } elseif ($faculty && $faculty->isHOD()) {
            $query = Attendance::getAttendanceOfDepartment(
                $faculty->department->code,
                $from,
                $to
            );
        } elseif ($faculty) {
            $query = Attendance::getAttendanceOfFaculty(
                $faculty->id,
                $from,
                $to
            );
        }

        $query = $query->get();

        if ($faculty?->isStaffAdvisor()) {
            $query = $query->concat(
                Attendance::getAttendanceOfClassroom(
                    $faculty->advisor_classroom_id,
                    $from,
                    $to
                )->get()
            );
        }

        return $query;
    }

    private function isEditableAttendance($period, $faculty, $is_admin): bool
    {
        return $period->course->hasFaculty($faculty?->id) || $is_admin;
    }

    /**
     * @param $data
     * @param null $faculty
     * @param bool $is_admin
     * @return array
     */
    public function serializeCourse($data, $faculty = null, bool $is_admin = false): array
    {
        $new_data = [];
        foreach ($data as $period) {
            if (array_key_exists($period->course_id, $new_data)) {
                array_push($new_data[$period->course_id]["attendances"], $period);
            } else {
                $new_data[$period->course_id] = [
                    "id" => $period->course_id,
                    "faculties" => $period->course->faculties,
                    "subject" => $period->course->subject,
                    "semester" => $period->course->semester,
                    "attendances" => [$period],
                    "editable" => $this->isEditableAttendance($period, $faculty, $is_admin)
                ];
            }
        }
        return $new_data;
    }

    public function parseAttendanceInput($inp)
    {
        // This should be an excel/workbook parser
        // For now parse csv
        $data = explode(",", trim($inp));
        return array_filter($data, function ($val) {
            return $val != "";
        });
    }

    /**
     * Check if there's another REGULAR course entry for the same classroom at the same time
     * @param DateTime $date
     * @param int $hour
     * @param int $classroom_id
     * @return bool
     */
    public function attendanceIsConflicted(DateTime $date, int $hour, int $classroom_id): bool
    {
        return Attendance::where([
            "date" => $date,
            "hour" => $hour,
        ])->whereHas("course", function ($q) use ($classroom_id) {
            $q->where([
                "classroom_id" => $classroom_id,
                "type" => CourseTypes::REGULAR
            ]);
        })->first() != null;
    }

    /**
     * @param DateTime $date
     * @return bool
     */
    public function isDateInFuture(DateTime $date): bool
    {
        $today = date_create_from_format("Y-m-d", date("Y-m-d"));
        return $date > $today;
    }

    /**
     * Create new absentee and return as array
     * @param $course
     * @param int $absentee_id
     * @param int $attendance_id
     * @return array
     */
    public function getAbsenteeAsArray($course, int $absentee_id, int $attendance_id): array
    {
        $absentee = (new Absentee())->toArray();
        $absentee["student_admission_id"] = $course->curriculums->filter(
            function ($curriculum) use ($absentee_id) {
                return $curriculum->student->admission_id == $absentee_id;
            }
        )->first()->student->admission_id;

        $absentee["attendance_id"] = $attendance_id;

        return $absentee;
    }

    /**
     * @param Student $student
     * @param $authUser
     * @param string|null $from
     * @param string|null $to
     * @return mixed
     */
    public function getAttendanceOfStudent(Student $student, $authUser, ?string $from, ?string $to)
    {
        $attendance = Attendance::getAttendanceOfStudent($student->admission_id, $from, $to);
        $faculty = $authUser->faculty;

        // HOD of student's dept
        $is_hod = $faculty?->isHOD() && $student->department_id == $faculty->department_id;
        $is_staff_advisor = $faculty?->isStaffAdvisor() && $student->classroom_id == $faculty->advisor_classroom_id;

        if ($authUser->student || $is_hod || $authUser->isAdmin() || $is_staff_advisor || $faculty?->isPrincipal()) {
            // TODO: Add dean
            $attendance = $attendance->get();
        } elseif ($faculty) {
            // Filter by course
            $attendance = $attendance->whereHas('course.faculties', function ($q) use ($faculty) {
                $q->where('faculties.id', $faculty->id);
            })->get();
        }

        return $attendance;
    }

    /**
     * @param $attendance
     * @param string $student_admission_id
     * @param $faculty
     * @param bool $is_admin
     */
    public function serializeAttendanceFromStudent($attendance, string $student_admission_id, $faculty, bool $is_admin)
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
            if ($this->isEditableAttendance($period, $faculty, $is_admin)) {
                // Only allow that particular faculty or admin to edit
                $period->editable = true;
            } else {
                $period->editable = false;
            }
        }
    }

    public function getStudentsFromCurriculums($curriculums)
    {
        return array_map(
            function ($curriculum) {
                return $curriculum["student"];
            },
            $curriculums->toArray()
        );
    }

    /**
     * Create new absentee for attendance
     * @param $attendance
     * @param string $admissionId
     * @return Absentee
     * @throws Exception
     */
    public function createAbsentee($attendance, string $admissionId): Absentee
    {
        $student_curriculum = $attendance->course->curriculums->firstWhere(
            "student_admission_id",
            $admissionId
        );
        if (!$student_curriculum) {
            throw new IntendedException(
                `Student with admission id $admissionId doesn't exist, or isn't enrolled in your class`
            );
        }
        $absentee = new Absentee();
        $absentee->attendance()->associate($attendance);
        $absentee->student()->associate($student_curriculum->student);

        return $absentee;
    }
}
