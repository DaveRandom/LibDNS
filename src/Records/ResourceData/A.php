<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
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

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return (string)$record->address;
    }

    public static function protocolDecode(DecodingContext $ctx): self
    {
        return new self(\DaveRandom\LibDNS\decode_ipv4address($ctx));
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        \DaveRandom\LibDNS\encode_ipv4address($ctx, $record->address);
    }
}
