<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;

final class TXT implements ResourceData
{
    const TYPE_ID = 16;

    private $strings;

    /**
     * @param string[] $strings
     */
    public function __construct(array $strings)
    {
        $this->strings = $strings;
    }

    /**
     * @return string[]
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
