<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CourseTypes extends Enum
{
    public const REGULAR = "regular";
    public const REGULAR_ELECTIVE = "elective";
    public const HONORS = "honors";
    public const MINOR = "minor";
}
