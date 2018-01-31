<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Protocol\Decoding;

use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;

final class ResourceDataDecoder
{
    const DEFAULT_DECODERS = [
        ResourceTypes::A => [ResourceData\A::class, 'protocolDecode'],
        ResourceTypes::AAAA => [ResourceData\AAAA::class, 'protocolDecode'],
        ResourceTypes::CNAME => [ResourceData\CNAME::class, 'protocolDecode'],
        ResourceTypes::DNAME => [ResourceData\DNAME::class, 'protocolDecode'],
        ResourceTypes::MX => [ResourceData\MX::class, 'protocolDecode'],
        ResourceTypes::NAPTR => [ResourceData\NAPTR::class, 'protocolDecode'],
        ResourceTypes::NS => [ResourceData\NS::class, 'protocolDecode'],
        ResourceTypes::PTR => [ResourceData\PTR::class, 'protocolDecode'],
        ResourceTypes::RP => [ResourceData\RP::class, 'protocolDecode'],
        ResourceTypes::SOA => [ResourceData\SOA::class, 'protocolDecode'],
        ResourceTypes::SRV => [ResourceData\SRV::class, 'protocolDecode'],
        ResourceTypes::TXT => [ResourceData\TXT::class, 'protocolDecode'],
    ];

    private static $callbackType;

    private $decoders = self::DEFAULT_DECODERS;

    public function __construct()
    {
        if (!isset(self::$callbackType)) {
            self::$callbackType = new CallbackType(
                new ReturnType(ResourceData::class),
                new ParameterType('context', DecodingContext::class),
                new ParameterType('length', BuiltInTypes::INT)
            );
        }
    }

    public function registerDecoder(int $type, callable $decoder)
    {
        if (!self::$callbackType->isSatisfiedBy($decoder)) {
            throw new \LogicException(
                'Callback with signature ' . CallbackType::createFromCallable($decoder)
                . ' does not satisfy required signature ' . self::$callbackType
            );
        }

        $this->decoders[$type] = $decoder;
    }

    public function hasDecoderForType(int $type): bool
    {
        return isset($this->decoders[$type]);
    }

    public function restoreDefaultDecoder(int $type)
    {
        $this->decoders[$type] = self::DEFAULT_DECODERS[$type] ?? null;
    }

    public function decode(Context $ctx, int $type, int $length): ResourceData
    {
        if (!isset($this->decoders[$type])) {
            return new ResourceData\UnknownResourceData($type, $ctx->getData($length));
        }

        $expectedOffset = $ctx->offset + $length;
        $result = ($this->decoders[$type])($ctx, $length);

        if ($ctx->offset !== $expectedOffset) {
            throw new \RuntimeException(
                "Current offset {$ctx->offset} does not match expected offset {$expectedOffset}"
            );
        }

        return $result;
    }
}
