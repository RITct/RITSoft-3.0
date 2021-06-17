<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Degrees extends Enum
{
    public const BTECH = "B.Tech";
    public const MTECH = "M.Tech";
    public const BARCH = "B.Arch";
    public const MCA = "MCA";
    public const PHD = "PhD";
}
