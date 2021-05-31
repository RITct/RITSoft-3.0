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

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function absentees(){
        return $this->hasMany(Absentee::class);
    }

    public static function getAttendanceOfStudent($admission_id, $from_date=null, $to_date=null){

        $base_query = Attendance::with(['absentees', 'course.subject', 'course.curriculums.student']);

        if($from_date) {
            if(!$to_date)
                $to_date = date("Y-m-d");

            $base_query = $base_query->whereBetween('date', [$from_date, $to_date]);
        }
        return $base_query
            ->whereHas('course.curriculums.student', function ($q) use ($admission_id) {
                $q->where('admission_id', $admission_id);
            })->get();
    }
}
