<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Roles extends Enum
{
    const Admin = "admin";
    const Principal = "principal";
    const HOD = "hod";
    const StaffAdvisor = "staff_advisor";
    const Faculty = "faculty";
    const Student = "student";
}
