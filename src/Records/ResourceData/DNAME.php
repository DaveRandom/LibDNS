<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;

final class DNAME implements ResourceData
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

    public function getTypeId(): int
    {
        return ResourceTypes::DNAME;
    }

    public static function decode(DecodingContext $ctx): DNAME
    {
        return new DNAME(\DaveRandom\LibDNS\decode_domain_name($ctx));
    }

    public static function encode(EncodingContext $ctx, DNAME $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getCanonicalName());
    }
}
