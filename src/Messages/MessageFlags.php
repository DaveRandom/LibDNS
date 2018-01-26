<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

use DaveRandom\Enum\Enum;

final class MessageFlags extends Enum
{
    const IS_RESPONSE = 0x8000;
    const IS_AUTHORITATIVE = 0x0400;
    const IS_TRUNCATED = 0x0200;
    const IS_RECURSION_DESIRED = 0x0100;
    const IS_RECURSION_AVAILABLE = 0x0080;
}
