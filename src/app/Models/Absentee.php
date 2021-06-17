<?php

namespace App\Models;

use App\Enums\LeaveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absentee extends Model
{
    use HasFactory;

    protected $table = "absentees";

    protected $attributes = [
        'leave_excuse' => LeaveType::NO_EXCUSE
    ];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
