<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Enum\Enum;

abstract class ResourceClasses extends Enum
{
    const IN = 1;
    const CH = 3;
    const HS = 4;
}
