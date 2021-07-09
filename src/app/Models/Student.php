<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends PersonalData
{
    use HasFactory;

    protected $primaryKey = 'admission_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [
        'roll_no'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function absentDates()
    {
        return $this->hasMany(Absentee::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function curriculums()
    {
        return $this->hasMany(Curriculum::class);
    }

    public function semester()
    {
        return $this->classroom->semester;
    }

    public function department()
    {
        return $this->classroom->department;
    }

    public function hasCourse($courseId): bool
    {
        $targetCourse = $this?->curriculums?->map(function ($curriculum) {
            return $curriculum->course_id;
        })?->filter(function ($studentCourseId) use ($courseId) {
            return $studentCourseId == $courseId;
        });

        return $targetCourse != null && !$targetCourse->isEmpty();
    }

    public function finishFeedback($courseId)
    {
        echo json_encode($this->curriculums);
        $targetCurriculum = $this->curriculums->firstWhere("course_id", $courseId);
        $targetCurriculum->is_feedback_complete = true;
        $targetCurriculum->save();
    }
}
