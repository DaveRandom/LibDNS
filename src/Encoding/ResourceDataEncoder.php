<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

use function DaveRandom\LibDNS\encode_character_data;
use function DaveRandom\LibDNS\encode_domain_name;
use function DaveRandom\LibDNS\encode_ipv4address;
use DaveRandom\LibDNS\Records\ResourceData;

final class ResourceDataEncoder
{
    const ENCODERS = [
        ResourceData\A::class => 'encodeA', /** @uses encodeA */
        ResourceData\CNAME::class => 'encodeCNAME', /** @uses encodeCNAME */
        ResourceData\MB::class => 'encodeMB', /** @uses encodeMB */
        ResourceData\MD::class => 'encodeMD', /** @uses encodeMD */
        ResourceData\MF::class => 'encodeMF', /** @uses encodeMF */
        ResourceData\MG::class => 'encodeMG', /** @uses encodeMG */
        ResourceData\MR::class => 'encodeMR', /** @uses encodeMR */
        ResourceData\NS::class => 'encodeNS', /** @uses encodeNS */
        ResourceData\NULLRecord::class => 'encodeNULL', /** @uses encodeNULL */
        ResourceData\SOA::class => 'encodeSOA', /** @uses encodeSOA */
        ResourceData\TXT::class => 'encodeTXT', /** @uses encodeTXT */
    ];

    private function encodeA(EncodingContext $ctx, ResourceData\A $data)
    {
        encode_ipv4address($ctx, $data->getAddress());
    }

    private function encodeCNAME(EncodingContext $ctx, ResourceData\CNAME $data)
    {
        encode_domain_name($ctx, $data->getCanonicalName());
    }

    private function encodeMB(EncodingContext $ctx, ResourceData\MB $data)
    {
        encode_domain_name($ctx, $data->getMailAgentName());
    }

    private function encodeMD(EncodingContext $ctx, ResourceData\MD $data)
    {
        encode_domain_name($ctx, $data->getMailAgentName());
    }

    private function encodeMF(EncodingContext $ctx, ResourceData\MF $data)
    {
        encode_domain_name($ctx, $data->getMailAgentName());
    }

    private function encodeMG(EncodingContext $ctx, ResourceData\MG $data)
    {
        encode_domain_name($ctx, $data->getMailboxName());
    }

    private function encodeMR(EncodingContext $ctx, ResourceData\MR $data)
    {
        encode_domain_name($ctx, $data->getMailboxName());
    }

    private function encodeNS(EncodingContext $ctx, ResourceData\NS $data)
    {
        encode_domain_name($ctx, $data->getAuthoritativeServerName());
    }

    private function encodeNULL(EncodingContext $ctx, ResourceData\NULLRecord $data)
    {
        $ctx->appendData($data->getData());
    }

    private function encodeSOA(EncodingContext $ctx, ResourceData\SOA $data)
    {
        encode_domain_name($ctx, $data->getMasterServerName());
        encode_domain_name($ctx, $data->getResponsibleMailAddress());

        $ctx->appendData(\pack(
            'N5',
            $data->getSerial(),
            $data->getRefreshInterval(),
            $data->getRetryInterval(),
            $data->getExpireTimeout(),
            $data->getTtl()
        ));
    }

    private function encodeTXT(EncodingContext $ctx, ResourceData\TXT $data)
    {
        foreach ($data->getStrings() as $string) {
            encode_character_data($ctx, $string);
        }
    }

    public function encode(EncodingContext $ctx, ResourceData $data)
    {
        $class = \get_class($data);

        if (!\array_key_exists($class, self::ENCODERS)) {
            throw new \UnexpectedValueException("Unknown resource data type: {$class}");
        }

        ([$this, self::ENCODERS[$class]])($ctx, $data);
    }
}
