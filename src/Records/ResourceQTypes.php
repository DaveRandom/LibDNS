<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

final class ResourceQTypes extends ResourceTypes
{
    // RFC 1035
    const AXFR  = 252;
    const ALL   = 255;

    // RFC 1996
    const IXFR  = 251;

    /** @deprecated */ const MAILB = 253;
    /** @deprecated */ const MAILA = 254;
}
