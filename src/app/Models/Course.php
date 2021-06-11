<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function MongoDB\BSON\toRelaxedExtendedJSON;

class Course extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [
        'semester',
        'active' => true
    ];

    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function curriculums(){
        return $this->hasMany(Curriculum::class);
    }

    static function get_base_query(){
        return Course::with("subject")->where("active", true);
    }
}
