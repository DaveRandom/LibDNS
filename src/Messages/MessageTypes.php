<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

use DaveRandom\Enum\Enum;

final class MessageTypes extends Enum
{
    const QUERY = 0;
    const RESPONSE = 1;
}
