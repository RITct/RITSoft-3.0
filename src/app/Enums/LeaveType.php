<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LeaveType extends Enum
{
    public const DUTY_LEAVE = "duty_leave";
    public const MEDICAL_LEAVE = "medical_leave";
    public const NO_EXCUSE = "no_excuse";
}
