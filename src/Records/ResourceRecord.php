<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use const DaveRandom\LibDNS\UINT32_MASK;
use DaveRandom\Network\DomainName;

final class ResourceRecord extends Record
{
    private $ttl;
    private $data;

    public function __construct(DomainName $name, int $type, int $class, int $ttl, ResourceData $data)
    {
        parent::__construct($name, $type, $class);

        if (($ttl & UINT32_MASK) !== $ttl) {
            throw new \InvalidArgumentException('Record class must be in the range 0 - 4294967296');
        }

        $this->ttl = $ttl;
        $this->data = $data;
    }

    public function getTTL(): int
    {
        return $this->ttl;
    }

    public function getData(): ResourceData
    {
        return $this->data;
    }
}
