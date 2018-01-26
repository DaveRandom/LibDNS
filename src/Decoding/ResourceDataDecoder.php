<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Decoding;

use function DaveRandom\LibDNS\decode_domain_name;
use function DaveRandom\LibDNS\decode_ipv4address;
use DaveRandom\LibDNS\Records\ResourceData;

final class ResourceDataDecoder
{
    const DECODERS = [
        ResourceData\A::TYPE_ID => 'decodeA', /** @uses decodeA */
        ResourceData\NS::TYPE_ID => 'decodeNS', /** @uses decodeNS */
        ResourceData\SOA::TYPE_ID => 'decodeSOA', /** @uses decodeSOA */
    ];

    private function decodeA(DecodingContext $ctx): ResourceData\A
    {
        return new ResourceData\A(decode_ipv4address($ctx));
    }

    private function decodeNS(DecodingContext $ctx): ResourceData\NS
    {
        return new ResourceData\NS(decode_domain_name($ctx));
    }

    private function decodeSOA(DecodingContext $ctx): ResourceData\SOA
    {
        $masterServerName = decode_domain_name($ctx);
        $responsibleMailAddress = decode_domain_name($ctx);
        $meta = $ctx->unpack('Nserial/Nrefresh/Nretry/Nexpire/Nttl', 20);

        return new ResourceData\SOA(
            $masterServerName,
            $responsibleMailAddress,
            $meta['serial'], $meta['refresh'], $meta['retry'], $meta['expire'], $meta['ttl'],
            false
        );
    }

    public function decode(DecodingContext $ctx, int $type, int $length): ResourceData
    {
        if (!\array_key_exists($type, self::DECODERS)) {
            throw new \UnexpectedValueException("Unknown resource data type ID: {$type}");
        }

        return ([$this, self::DECODERS[$type]])($ctx, $length);
    }
}
