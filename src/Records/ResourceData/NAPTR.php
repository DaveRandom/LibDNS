<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;

final class NAPTR implements ResourceData
{
    private $order;
    private $preference;
    private $flags;
    private $services;
    private $regex;
    private $replacement;

    public function __construct(int $order, int $preference, string $flags, string $services, string $regex, DomainName $replacement)
    {
        $this->order = \DaveRandom\LibDNS\validate_uint16('Order', $order);
        $this->preference = \DaveRandom\LibDNS\validate_uint16('Preference', $preference);
        $this->flags = $flags;
        $this->services = $services;
        $this->regex = $regex;
        $this->replacement = $replacement;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getPreference(): int
    {
        return $this->preference;
    }

    public function getFlags(): string
    {
        return $this->flags;
    }

    public function getServices(): string
    {
        return $this->services;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function getReplacement(): DomainName
    {
        return $this->replacement;
    }

    public function getTypeId(): int
    {
        return ResourceTypes::NAPTR;
    }

    public static function decode(DecodingContext $ctx): NAPTR
    {
        $parts = $ctx->unpack('norder/npreference', 4);
        $flags = \DaveRandom\LibDNS\decode_character_string($ctx);
        $services = \DaveRandom\LibDNS\decode_character_string($ctx);
        $regex = \DaveRandom\LibDNS\decode_character_string($ctx);
        $replacement = \DaveRandom\LibDNS\decode_domain_name($ctx);

        return new NAPTR($parts['order'], $parts['preference'], $flags, $services, $regex, $replacement);
    }

    public static function encode(EncodingContext $ctx, NAPTR $record)
    {
        $ctx->appendData(\pack('n2', $record->order, $record->preference));
        \DaveRandom\LibDNS\encode_character_string($ctx, $record->flags);
        \DaveRandom\LibDNS\encode_character_string($ctx, $record->services);
        \DaveRandom\LibDNS\encode_character_string($ctx, $record->regex);
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->replacement);
    }
}
