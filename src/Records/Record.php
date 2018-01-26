<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Network\DomainName;
use const DaveRandom\LibDNS\UINT16_MASK;

abstract class Record
{
    private $name;
    private $type;
    private $class;

    protected function __construct(DomainName $name, int $type, int $class)
    {
        if (($class & UINT16_MASK) !== $class) {
            throw new \InvalidArgumentException('Record class must be in the range 0 - 65535');
        }

        $this->name = $name;
        $this->type = $type;
        $this->class = $class;
    }

    public function getName(): DomainName
    {
        return $this->name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getClass(): int
    {
        return $this->class;
    }
}
