<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class RequestStates extends Enum
{
    public const PENDING = "pending";
    public const APPROVED = "approved";
    public const REJECTED = "rejected";
}
