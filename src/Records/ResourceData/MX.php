<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;

final class MX implements ResourceData
{
    private $preference;
    private $exchange;

    public function __construct(int $preference, DomainName $exchange)
    {
        $this->preference = $preference;
        $this->exchange = $exchange;
    }

    public function getPreference(): int
    {
        return $this->preference;
    }

    public function getExchange(): DomainName
    {
        return $this->exchange;
    }

    public function getTypeId(): int
    {
        return ResourceTypes::MX;
    }

    public static function decode(DecodingContext $ctx): MX
    {
        $preference = $ctx->unpack('n', 2)[1];
        $exchange = \DaveRandom\LibDNS\decode_domain_name($ctx);

        return new MX($preference, $exchange);
    }

    public static function encode(EncodingContext $ctx, MX $data)
    {
        $ctx->appendData(\pack('n', $data->getPreference()));
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getExchange());
    }
}
