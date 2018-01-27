<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
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

    public function getTypeId(): int
    {
        return ResourceTypes::NS;
    }

    public static function decode(DecodingContext $ctx): NS
    {
        return new NS(\DaveRandom\LibDNS\decode_domain_name($ctx));
    }

    public static function encode(EncodingContext $ctx, NS $record)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->getAuthoritativeServerName());
    }
}
