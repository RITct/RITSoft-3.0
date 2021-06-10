<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
    ];

    protected $guarded = [
        'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Hash Password
    public function setPasswordAttribute($password){
        $this->attributes['password'] = bcrypt($password);
    }

    public function student(){
        return $this->hasOne(Student::class);
    }

    public function faculty(){
        return $this->hasOne(Faculty::class);
    }

    public function get_profile($authType=null){
        $profileType = $authType ? $authType : $this->roles->first()->name;
        switch ($profileType){
            case Roles::Student: return $this->student->first();
            // Map roles as we create them
        }
        return null;
    }

    public function has_multiple_profiles(){
        return count($this->roles) > 1;
    }

    public function is_admin(){
        return $this->hasRole(Roles::Admin);
    }

    public function name(){
        $profile = $this->get_profile();
        if($profile)
            return $profile->name;
        return "";
    }
}
