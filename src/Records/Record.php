<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Network\DomainName;

abstract class Record
{
    private $name;
    private $type;
    private $class;

    /** @internal */
    protected function __construct(DomainName $name, int $type, int $class)
    {
        $this->name = $name;
        $this->type = $type;
        $this->class = \DaveRandom\LibDNS\validate_uint16('Record class', $class);
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
