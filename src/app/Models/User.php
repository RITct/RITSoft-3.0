<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'email'
    ];

    protected $guarded = [
        'is_active'
    ];

    protected $with = [
        'roles',
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
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function officeStaff()
    {
        return $this->belongsTo(OfficeStaff::class);
    }

    public function getProfile()
    {
        $profileType = $this->roles->first()->name;
        switch ($profileType) {
            case Roles::STUDENT:
                return $this->student;
            case Roles::OFFICE:
                return $this->officeStaff;
            // Faculty has so many roles covered, HOD, PRINCIPAL, DEAN etc
            default:
                return $this->faculty;
        }
    }

    public function hasMultipleProfiles()
    {
        return count($this->roles) > 1;
    }

    public function isAdmin()
    {
        return $this->hasRole(Roles::ADMIN);
    }

    public function name()
    {
        $profile = $this->getProfile();
        if ($profile) {
            return $profile->name;
        }
        return "";
    }
}
