<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class RequestTypes extends Enum
{
    public const STUDENT_PHOTO_UPLOAD = "student_photo_upload";
    public const SEMESTER_REGISTRATION = "semester_registration";
    public const TEST_REQUEST = "test_request";
}
