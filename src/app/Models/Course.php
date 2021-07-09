<?php

namespace App\Models;

use App\Enums\CourseTypes;
use App\Enums\FeedbackQuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [
        'semester',
        'type',
        'active' => true,
        'is_feedback_open' => false
    ];

    protected $with = ["faculties"];

    static array $defaultFeedbackFormat = [
        [
            "question" => "Question 1",
            "type" => FeedbackQuestionType::MCQ,
            "options" => [
                ["string" => "option1", "score" => 1],
                ["string" => "option2", "score" => 2],
                ["string" => "option3", "score" => 3],
                ["string" => "option4", "score" => 4],
            ],
            "required" => true
        ],
        [
            "question" => "Question 2",
            "type" => FeedbackQuestionType::BOOLEAN,
            "required" => true
        ],
        [
            "question" => "Question 3",
            "type" => FeedbackQuestionType::TEXT,
            "required" => true
        ],
    ];

    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, "faculty_course");
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

    public function hasFaculty($facultyId): bool
    {
        return $this->faculties()->find($facultyId) != null;
    }

    public static function getBaseQuery()
    {
        return Course::with("subject")->where("active", true);
    }

    public function getFeedbackFormat(): array
    {
        return json_decode($this->feedback_format) ?? Course::$defaultFeedbackFormat;
    }
}
