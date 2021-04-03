<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = "attendance";

    protected $attributes = [
        "date",
        "hour",
        "duty_leave"
    ];

    /*public function subject(){
        $this->belongsTo(Subject::class)
    }*/
    /**
     * @return BelongsTo
     */
    public function student(){
       return $this->belongsTo(Student::class);
    }

    /*public function class(){
        return $this->belongsTo(Class:class)
    }*/
}
