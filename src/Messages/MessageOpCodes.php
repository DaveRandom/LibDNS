<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

use DaveRandom\Enum\Enum;

final class MessageOpCodes extends Enum
{
    const QUERY = 0;
    const STATUS = 2;
    const NOTIFY = 4;
    const UPDATE = 5;

    /** @deprecated */ const IQUERY = 1;
}
