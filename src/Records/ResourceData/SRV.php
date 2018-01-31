<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class SRV implements ResourceData
{
    private $priority;
    private $weight;
    private $port;
    private $target;

    public function __construct(int $priority, int $weight, int $port, DomainName $target)
    {
        $this->priority = \DaveRandom\LibDNS\validate_uint16('Priority', $priority);
        $this->weight = \DaveRandom\LibDNS\validate_uint16('Weight', $weight);
        $this->port = \DaveRandom\LibDNS\validate_uint16('Port', $port);
        $this->target = $target;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getTarget(): DomainName
    {
        return $this->target;
    }

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return "{$record->priority} {$record->weight} {$record->port} {$record->target}.";
    }

    public static function protocolDecode(DecodingContext $ctx): self
    {
        $parts = $ctx->unpack('npriority/nweight/nport', 6);
        $target = \DaveRandom\LibDNS\decode_domain_name($ctx);

        return new self($parts['priority'], $parts['weight'], $parts['port'], $target);
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        $ctx->appendData(\pack('n3', $record->priority, $record->weight, $record->port));
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->target);
    }
}
