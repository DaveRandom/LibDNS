<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Decoding;

use function DaveRandom\LibDNS\decode_domain_name;
use function DaveRandom\LibDNS\decode_ipv4address;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceData\A;
use DaveRandom\LibDNS\Records\ResourceData\SOA;

final class ResourceDataDecoder
{
    /**
     * @uses decodeA
     * @uses decodeSOA
     */
    private static $DECODERS = [
        A::TYPE_ID => 'decodeA',
        SOA::TYPE_ID => 'decodeSOA',
    ];

    private function decodeA(DecodingContext $ctx): A
    {
        return new A(decode_ipv4address($ctx));
    }

    private function decodeSOA(DecodingContext $ctx): SOA
    {
        $masterServerName = decode_domain_name($ctx);
        $responsibleMailAddress = decode_domain_name($ctx);
        $meta = $ctx->unpack('Nserial/Nrefresh/Nretry/Nexpire/Nttl', 20);

        return new SOA(
            $masterServerName,
            $responsibleMailAddress,
            $meta['serial'], $meta['refresh'], $meta['retry'], $meta['expire'], $meta['ttl'],
            false
        );
    }

    public function decode(DecodingContext $ctx, int $type, int $length): ResourceData
    {
        if (!\array_key_exists($type, self::$DECODERS)) {
            throw new \UnexpectedValueException("Unknown resource data type ID: {$type}");
        }

        return ([$this, self::$DECODERS[$type]])($ctx, $length);
    }
}
