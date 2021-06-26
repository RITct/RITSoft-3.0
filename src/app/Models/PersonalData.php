<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalData extends Model
{
    protected $fillable = ["name", "phone", "address", "photo_url"];

    public function updateProfileImage($imgUrl)
    {
        $this->photo_url = $imgUrl;
        $this->save();
    }
}
