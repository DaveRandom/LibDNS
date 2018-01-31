<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class PTR implements ResourceData
{
    private $name;

    public function __construct(DomainName $name)
    {
        $this->name = $name;
    }

    public function getName(): DomainName
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return "{$record->name}.";
    }

    public static function protocolDecode(DecodingContext $ctx): self
    {
        return new self(\DaveRandom\LibDNS\decode_domain_name($ctx));
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->getName());
    }
}
