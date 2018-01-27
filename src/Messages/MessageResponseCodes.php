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
    const UNEXPECTED_EXTANT_DOMAIN = 6;
    const UNEXPECTED_EXTANT_RRSET = 7;
    const NON_EXISTENT_RRSET = 8;
    const SERVER_NOT_AUTHORITATIVE = 9;
    const NOT_AUTHORIZED = 9;
    const BAD_OPT_VERSION = 16;
    const BAD_SIGNATURE = 16;
    const BAD_KEY = 17;
    const BAD_TIME = 18;
    const BAD_MODE = 19;
    const BAD_NAME = 20;
    const BAD_ALGORITHM = 21;
    const BAD_TRUNCATION = 22;
    const BAD_COOKIE = 23;
}
