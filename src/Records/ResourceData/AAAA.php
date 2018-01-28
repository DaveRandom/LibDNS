<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\IPv6Address;

final class AAAA implements ResourceData
{
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
        return ResourceTypes::AAAA;
    }

    public static function decode(DecodingContext $ctx): AAAA
    {
        return new AAAA(\DaveRandom\LibDNS\decode_ipv6address($ctx));
    }

    public static function encode(EncodingContext $ctx, AAAA $record)
    {
        \DaveRandom\LibDNS\encode_ipv6address($ctx, $record->address);
    }
}
