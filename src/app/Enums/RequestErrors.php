<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class RequestErrors extends Enum
{
    public const ATTEMPT_TO_UPDATE_NON_PENDING_REQUEST = 100;
    public const ATTEMPT_TO_ALTER_PAYLOAD_WITHOUT_APPROVAL = 101;
}
