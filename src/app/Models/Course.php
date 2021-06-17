<?php

namespace App\Models;

use App\Enums\CourseTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [
        'semester',
        'type',
        'active' => true
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function curriculums(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function hours(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getTargetDepartment(): Department
    {
        // Department of students studying the course
        return $this->classroom->department;
    }

    public function getSubjectDepartment(): Department
    {
        return $this->subject->department;
    }
    public function isAnElective(): bool
    {
        return $this->type != CourseTypes::REGULAR;
    }
    public static function getBaseQuery()
    {
        return Course::with("subject")->where("active", true);
    }
}
