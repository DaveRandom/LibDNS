<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class CNAME implements ResourceData
{
    private $canonicalName;

    public function __construct(DomainName $canonicalName)
    {
        $this->canonicalName = $canonicalName;
    }

    public function getCanonicalName(): DomainName
    {
        return $this->canonicalName;
    }

    public static function decode(DecodingContext $ctx): CNAME
    {
        return new CNAME(\DaveRandom\LibDNS\decode_domain_name($ctx));
    }

    public static function encode(EncodingContext $ctx, CNAME $record)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->canonicalName);
    }
}
