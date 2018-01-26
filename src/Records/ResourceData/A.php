<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\IPv4Address;

final class A implements ResourceData
{
    const TYPE_ID = 1;

    private $address;

    public function __construct(IPv4Address $address)
    {
        $this->address = $address;
    }

    public function getAddress(): IPv4Address
    {
        return $this->address;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
