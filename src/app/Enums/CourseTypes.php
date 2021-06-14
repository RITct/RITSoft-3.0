<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CourseTypes extends Enum
{
    const Regular = "regular";
    const RegularElective = "elective";
    const Honors = "honors";
    const Minor = "minor";
}
