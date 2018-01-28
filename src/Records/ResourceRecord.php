<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Network\DomainName;

final class ResourceRecord extends Record
{
    private $ttl;
    private $data;

    public function __construct(DomainName $name, int $type, int $class, int $ttl, ResourceData $data)
    {
        /** @noinspection PhpInternalEntityUsedInspection */
        parent::__construct($name, $type, $class);

        $this->ttl = \DaveRandom\LibDNS\validate_uint32('Time-to-live', $ttl);
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
