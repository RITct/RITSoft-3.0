<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Roles extends Enum
{
    public const ADMIN = "admin";
    public const PRINCIPAL = "principal";
    public const HOD = "hod";
    public const STAFF_ADVISOR = "staff_advisor";
    public const FACULTY = "faculty";
    public const STUDENT = "student";
}
