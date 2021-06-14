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
    const BTECH =   "B.Tech";
    const MTECH =   "M.Tech";
    const BARCH =   "B.Arch";
    const MCA =     "MCA";
    const PHD =     "PhD";
}
