<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

use DaveRandom\LibDNS\Records\ResourceData;

final class ResourceDataEncoder
{
    const ENCODERS = [
        ResourceData\A::class => 'encodeA', /** @uses encodeA */
        ResourceData\AAAA::class => 'encodeAAAA', /** @uses encodeAAAA */
        ResourceData\CNAME::class => 'encodeCNAME', /** @uses encodeCNAME */
        ResourceData\CNAME::class => 'encodeDNAME', /** @uses encodeDNAME */
        ResourceData\HINFO::class => 'encodeHINFO', /** @uses encodeHINFO */
        ResourceData\MB::class => 'encodeMB', /** @uses encodeMB */
        ResourceData\MD::class => 'encodeMD', /** @uses encodeMD */
        ResourceData\MF::class => 'encodeMF', /** @uses encodeMF */
        ResourceData\MG::class => 'encodeMG', /** @uses encodeMG */
        ResourceData\MINFO::class => 'encodeMINFO', /** @uses encodeMINFO */
        ResourceData\MR::class => 'encodeMR', /** @uses encodeMR */
        ResourceData\MX::class => 'encodeMX', /** @uses encodeMX */
        ResourceData\NS::class => 'encodeNS', /** @uses encodeNS */
        ResourceData\NULLRecord::class => 'encodeNULL', /** @uses encodeNULL */
        ResourceData\PTR::class => 'encodePTR', /** @uses encodePTR */
        ResourceData\SOA::class => 'encodeSOA', /** @uses encodeSOA */
        ResourceData\TXT::class => 'encodeTXT', /** @uses encodeTXT */
        ResourceData\WKS::class => 'encodeWKS', /** @uses encodeWKS */
    ];

    private function encodeA(EncodingContext $ctx, ResourceData\A $data)
    {
        \DaveRandom\LibDNS\encode_ipv4address($ctx, $data->getAddress());
    }

    private function encodeAAAA(EncodingContext $ctx, ResourceData\AAAA $data)
    {
        \DaveRandom\LibDNS\encode_ipv6address($ctx, $data->getAddress());
    }

    private function encodeCNAME(EncodingContext $ctx, ResourceData\CNAME $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getCanonicalName());
    }

    private function encodeDNAME(EncodingContext $ctx, ResourceData\DNAME $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getCanonicalName());
    }

    private function encodeHINFO(EncodingContext $ctx, ResourceData\HINFO $data)
    {
        \DaveRandom\LibDNS\encode_character_data($ctx, $data->getCpu());
        \DaveRandom\LibDNS\encode_character_data($ctx, $data->getOs());
    }

    private function encodeMB(EncodingContext $ctx, ResourceData\MB $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getMailAgentName());
    }

    private function encodeMD(EncodingContext $ctx, ResourceData\MD $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getMailAgentName());
    }

    private function encodeMF(EncodingContext $ctx, ResourceData\MF $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getMailAgentName());
    }

    private function encodeMG(EncodingContext $ctx, ResourceData\MG $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getMailboxName());
    }

    private function encodeMINFO(EncodingContext $ctx, ResourceData\MINFO $data)
    {
        \DaveRandom\LibDNS\encode_character_data($ctx, $data->getResponsibleMailbox());
        \DaveRandom\LibDNS\encode_character_data($ctx, $data->getErrorMailbox());
    }

    private function encodeMR(EncodingContext $ctx, ResourceData\MR $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getMailboxName());
    }

    private function encodeMX(EncodingContext $ctx, ResourceData\MX $data)
    {
        $ctx->appendData(\pack('n', $data->getPreference()));
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getExchange());
    }

    private function encodeNS(EncodingContext $ctx, ResourceData\NS $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getAuthoritativeServerName());
    }

    private function encodeNULL(EncodingContext $ctx, ResourceData\NULLRecord $data)
    {
        $ctx->appendData($data->getData());
    }

    private function encodePTR(EncodingContext $ctx, ResourceData\PTR $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getName());
    }

    private function encodeSOA(EncodingContext $ctx, ResourceData\SOA $data)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getMasterServerName());
        \DaveRandom\LibDNS\encode_domain_name($ctx, $data->getResponsibleMailAddress());

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
            \DaveRandom\LibDNS\encode_character_data($ctx, $string);
        }
    }

    private function encodeWKS(EncodingContext $ctx, ResourceData\WKS $data)
    {
        \DaveRandom\LibDNS\encode_ipv4address($ctx, $data->getAddress());
        $ctx->appendData(\pack('Ca*', $data->getProtocol(), $data->getBitMap()));
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
