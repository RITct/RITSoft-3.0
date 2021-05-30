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

    public static function getAttendanceOfStudent($admission_id, $from_date_raw=null, $to_date_raw=null){
        if($from_date_raw) {
            $from_date = date_parse($from_date_raw);

            if($to_date_raw)
                $to_date = date_parse($to_date_raw);
            else
                $to_date = date("dd-mm-yyyy");

            return Attendance::with('Absentee')
                ->whereBetween('date', [$from_date, $to_date])
                ->whereHas('Absentee', function ($q) use ($admission_id) {
                    $q->where('student_admission_id', $admission_id);
                })->get();
        }
        return Attendance::with('Absentee')
            ->whereHas('Absentee', function ($q) use ($admission_id) {
                $q->where('student_admission_id', $admission_id);
            });
    }
}
