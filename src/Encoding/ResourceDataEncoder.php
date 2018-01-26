<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

use function DaveRandom\LibDNS\encode_ipv4address;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceData\A;

final class ResourceDataEncoder
{
    /**
     * @uses encodeA
     */
    private static $ENCODERS = [
        A::class => 'encodeA',
    ];

    private function encodeA(A $data): string
    {
        return encode_ipv4address($data->getAddress());
    }

    public function encode(EncodingContext $ctx, ResourceData $data): string
    {
        $class = \get_class($data);

        if (!\array_key_exists($class, self::$ENCODERS)) {
            throw new \UnexpectedValueException("Unknown resource data type: {$class}");
        }

        return ([$this, self::$ENCODERS[$class]])($data, $ctx);
    }
}
