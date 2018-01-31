<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class NS implements ResourceData
{
    private $authoritativeServerName;

    public function __construct(DomainName $authoritativeServerName)
    {
        $this->authoritativeServerName = $authoritativeServerName;
    }

    public function getAuthoritativeServerName(): DomainName
    {
        return $this->authoritativeServerName;
    }

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return "{$record->authoritativeServerName}.";
    }

    public static function protocolDecode(DecodingContext $ctx): self
    {
        return new self(\DaveRandom\LibDNS\decode_domain_name($ctx));
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->getAuthoritativeServerName());
    }
}
