<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\RawResourceData;

final class UnknownResourceData implements RawResourceData
{
    private $typeId;
    private $data;

    public function __construct(int $typeId, string $data)
    {
        $this->typeId = $typeId;
        $this->data = $data;
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
