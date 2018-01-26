<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Decoding;

use function DaveRandom\LibDNS\decode_ipv4address;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceData\A;

final class ResourceDataDecoder
{
    /**
     * @uses decodeA
     */
    private static $DECODERS = [
        A::TYPE_ID => 'decodeA',
    ];

    private function decodeA(DecodingContext $ctx): A
    {
        return new A(decode_ipv4address($ctx));
    }

    public function decode(DecodingContext $ctx, int $type, int $length): ResourceData
    {
        if (!\array_key_exists($type, self::$DECODERS)) {
            throw new \UnexpectedValueException("Unknown resource data type ID: {$type}");
        }

        return ([$this, self::$DECODERS[$type]])($ctx, $length);
    }
}
