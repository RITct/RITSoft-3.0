<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "attendance";

    protected $fillable = [
        "date",
        "hour",
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function absentees()
    {
        return $this->hasMany(Absentee::class);
    }

    protected static function filterByDate($base_query, $from_date, $to_date)
    {
        if ($from_date) {
            if (!$to_date) {
                $to_date = date("Y-m-d");
            }
            return $base_query->whereBetween('date', [$from_date, $to_date]);
        }
        return $base_query;
    }

    public static function getBaseQuery($from_date = null, $to_date = null)
    {
        // Get all related data, and select only active courses
        return Attendance::filterByDate(
            Attendance::with([
                'course.classroom',
                'course.subject',
                'course.curriculums.student',
                'course.faculty',
                'absentees'
            ]),
            $from_date,
            $to_date
        )
            ->whereHas("course", function ($q) {
                $q->where("active", true);
            });
    }

    public static function getAttendanceOfStudent($admission_id, $from_date = null, $to_date = null, $base_query = null)
    {
        // Returns Eloquent Query, call get() to execute
        if (!$base_query) {
            $base_query = Attendance::getBaseQuery($from_date, $to_date);
        }

        return $base_query
            ->whereHas('course.curriculums.student', function ($q) use ($admission_id) {
                $q->where('admission_id', $admission_id);
            });
    }

    public static function getAttendanceOfDepartment($dept_code, $from_date = null, $to_date = null, $base_query = null)
    {
        // Returns Eloquent Query, call get() to execute
        // CommonAttendance of all STUDENTS in department, NOT COURSE
        // Eg: If an EC minor is chosen by CS student, CS HOD can view attendance not EC HOD
        if (!$base_query) {
            $base_query = Attendance::getBaseQuery($from_date, $to_date);
        }

        return $base_query->whereHas('course.classroom', function ($q) use ($dept_code) {
            $q->where('department_code', $dept_code);
        });
    }

    public static function getAttendanceOfCourse($course_id, $from_date = null, $to_date = null, $base_query = null)
    {
        if (!$base_query) {
            $base_query = Attendance::getBaseQuery($from_date, $to_date);
        }

        return $base_query->where("course", $course_id);
    }

    public static function getAttendanceOfFaculty($faculty_id, $from_date = null, $to_date = null, $base_query = null)
    {
        if (!$base_query) {
            $base_query = Attendance::getBaseQuery($from_date, $to_date);
        }

        return $base_query->whereHas("course", function ($q) use ($faculty_id) {
            $q->where("faculty_id", $faculty_id);
        });
    }

    public static function getAttendanceOfClassroom($classroom_id, $from_date = null, $to_date = null, $base_query = null)
    {
        if(!$base_query) {
            $base_query = Attendance::getBaseQuery($from_date, $to_date);
        }

        return $base_query->whereHas("course.classroom", function ($q) use ($classroom_id) {
            return $q->where("id", $classroom_id);
        });
    }
}
