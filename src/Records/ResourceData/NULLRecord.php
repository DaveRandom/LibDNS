<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;

final class NULLRecord implements ResourceData
{
    const TYPE_ID = 10;

    private $data;

    public function __construct(string $data)
    {
        if (\strlen($data) > 65535) {
            throw new \InvalidArgumentException('NULL record data must be no more than 65535 bytes');
        }

        $this->data = $data;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
