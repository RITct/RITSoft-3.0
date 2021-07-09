<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        "data"
    ];

    static array $testFeedback = [
        3,
        0,
        "testing the system"
    ];

    public $timestamps = false;

    protected $table = "feedbacks";

    protected $with = ["course", "faculty"];

    protected $casts = ["data" => "json"];

    public function setDataAttribute($data)
    {
        $this->attributes["data"] = json_encode($data);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
}
