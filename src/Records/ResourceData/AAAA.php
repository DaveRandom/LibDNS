<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\IPv6Address;

final class AAAA implements ResourceData
{
    const TYPE_ID = 28;

    private $address;

    public function __construct(IPv6Address $address)
    {
        $this->address = $address;
    }

    public function getAddress(): IPv6Address
    {
        return $this->address;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
