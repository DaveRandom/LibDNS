<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\IPv4Address;

final class A implements ResourceData
{
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
        return ResourceTypes::A;
    }

    public static function decode(DecodingContext $ctx): A
    {
        return new A(\DaveRandom\LibDNS\decode_ipv4address($ctx));
    }

    public static function encode(EncodingContext $ctx, A $record)
    {
        \DaveRandom\LibDNS\encode_ipv4address($ctx, $record->address);
    }
}
