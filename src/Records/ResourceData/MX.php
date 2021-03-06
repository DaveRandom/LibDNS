<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
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

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return "{$record->preference} {$record->exchange}.";
    }

    public static function protocolDecode(DecodingContext $ctx): self
    {
        $preference = $ctx->unpack('n', 2)[1];
        $exchange = \DaveRandom\LibDNS\decode_domain_name($ctx);

        return new self($preference, $exchange);
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        $ctx->appendData(\pack('n', $record->getPreference()));
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->getExchange());
    }
}
