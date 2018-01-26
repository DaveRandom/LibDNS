<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

use DaveRandom\Enum\Enum;

final class MessageOpCodes extends Enum
{
    const QUERY = 0;
    const IQUERY = 1;
    const STATUS = 2;
}
