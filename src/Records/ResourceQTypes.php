<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

final class ResourceQTypes extends ResourceTypes
{
    const AXFR  = 252;
    const MAILB = 253;
    const MAILA = 254;
    const ALL   = 255;
}
