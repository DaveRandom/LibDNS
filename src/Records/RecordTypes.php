<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Enum\Enum;

final class RecordTypes extends Enum
{
    const QUESTION = 0;
    const RESOURCE = 1;
}
