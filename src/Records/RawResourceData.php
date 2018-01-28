<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

interface RawResourceData extends ResourceData
{
    function getData(): string;
}
