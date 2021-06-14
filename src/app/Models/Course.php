<?php

namespace App\Models;

use App\Enums\CourseTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [
        'semester',
        'type',
        'active' => true
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function curriculums()
    {
        return $this->hasMany(Curriculum::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function getTargetDepartment()
    {
        // Department of students studying the course
        return $this->classroom->department;
    }

    public function getSubjectDepartment()
    {
        return $this->subject->department;
    }
    public function isAnElective()
    {
        return $this->type != CourseTypes::REGULAR;
    }
    public static function getBaseQuery()
    {
        return Course::with("subject")->where("active", true);
    }
}
