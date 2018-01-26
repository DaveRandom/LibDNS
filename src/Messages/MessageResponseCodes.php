<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

use DaveRandom\Enum\Enum;

final class MessageResponseCodes extends Enum
{
    const NO_ERROR = 0;
    const FORMAT_ERROR = 1;
    const SERVER_FAILURE = 2;
    const NAME_ERROR = 3;
    const NOT_IMPLEMENTED = 4;
    const REFUSED = 5;
}
