<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;

final class HINFO implements ResourceData
{
    const TYPE_ID = 13;

    private $cpu;
    private $os;

    public function __construct(string $cpu, string $os)
    {
        $this->cpu = $cpu;
        $this->os = $os;
    }

    public function getCpu(): string
    {
        return $this->cpu;
    }

    public function getOs(): string
    {
        return $this->os;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
