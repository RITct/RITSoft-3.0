<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Setting primary key as admission number.
     */
    protected $primaryKey = 'admission_no';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $attributes = [
        'admission_no',
        'current_semester',
        'img_file',
    ];

    /**
     * One to one relation with Attendance table
     * Foreign key= student_id
     */
    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    /**
     * One to one relation with Sessional_marks table
     */
    public function sessional_marks()
    {
        return $this->hasOne(Sessional_marks::class);
    }

    /**
     * One to one relation with Series_marks table
     */
    public function series_marks()
    {
        return $this->hasOne(Series_marks::class);
    }

    /**
     * One to one relation with University_marks table
     */
    public function university_marks()
    {
        return $this->hasOne(University_marks::class);
    }

    /**
     * One to one relation with Feedback table
     */
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    /**
     * One to one relation with Semester_reg table
     */
    public function semester_reg()
    {
        return $this->hasOne(Semester_reg::class);
    }

}
