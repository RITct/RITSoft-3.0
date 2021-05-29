<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function curriculums(){
        return $this->hasMany(Curriculum::class);
    }
}
