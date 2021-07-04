<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class FeedbackQuestionType extends Enum
{
    public const TEXT = "text";
    public const BOOLEAN = "boolean";
    public const MCQ = "mcq";
}
