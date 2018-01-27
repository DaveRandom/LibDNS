<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
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

    public function getTypeId(): int
    {
        return ResourceTypes::PTR;
    }

    public static function decode(DecodingContext $ctx): PTR
    {
        return new PTR(\DaveRandom\LibDNS\decode_domain_name($ctx));
    }

    public static function encode(EncodingContext $ctx, PTR $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getName());
    }
}
