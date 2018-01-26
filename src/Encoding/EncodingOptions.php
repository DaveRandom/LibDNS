<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

use DaveRandom\Enum\Enum;

final class EncodingOptions extends Enum
{
    const NO_COMPRESSION = 0b01;
    const FORMAT_TCP     = 0b10;
}
