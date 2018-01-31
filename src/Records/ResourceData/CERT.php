<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;

final class CERT implements ResourceData
{
    private $type;
    private $keyTag;
    private $algorithm;
    private $certificate;

    public function __construct(int $type, int $keyTag, int $algorithm, string $certificate)
    {
        $this->type = \DaveRandom\LibDNS\validate_uint16('Certificate type', $type);
        $this->keyTag = \DaveRandom\LibDNS\validate_uint16('Key tag', $keyTag);
        $this->algorithm = \DaveRandom\LibDNS\validate_byte('Certificate algorithm', $algorithm);
        $this->certificate = $certificate;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getKeyTag(): int
    {
        return $this->keyTag;
    }

    public function getAlgorithm(): int
    {
        return $this->algorithm;
    }

    public function getCertificate(): string
    {
        return $this->certificate;
    }

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return "{$record->type} {$record->keyTag}. {$record->algorithm} " . \base64_encode($record->certificate);
    }

    public static function protocolDecode(DecodingContext $ctx, int $length): self
    {
        $certLength = $length - 5;
        $parts = $ctx->unpack("ntype/ntag/Calgo/Z{$certLength}cert", $length);

        return new self($parts['type'], $parts['tag'], $parts['algo'], $parts['cert']);
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        $ctx->appendData(\pack('n2CZ*', $record->type, $record->keyTag, $record->algorithm, $record->certificate));
    }
}
