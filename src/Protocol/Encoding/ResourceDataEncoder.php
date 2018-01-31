<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Protocol\Encoding;

use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\RawResourceData;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;

final class ResourceDataEncoder
{
    const DEFAULT_ENCODERS = [
        ResourceTypes::A => [ResourceData\A::class, 'protocolEncode'],
        ResourceTypes::AAAA => [ResourceData\AAAA::class, 'protocolEncode'],
        ResourceTypes::CNAME => [ResourceData\CNAME::class, 'protocolEncode'],
        ResourceTypes::CNAME => [ResourceData\DNAME::class, 'protocolEncode'],
        ResourceTypes::MX => [ResourceData\MX::class, 'protocolEncode'],
        ResourceTypes::NAPTR => [ResourceData\NAPTR::class, 'protocolEncode'],
        ResourceTypes::NS => [ResourceData\NS::class, 'protocolEncode'],
        ResourceTypes::PTR => [ResourceData\PTR::class, 'protocolEncode'],
        ResourceTypes::RP => [ResourceData\RP::class, 'protocolEncode'],
        ResourceTypes::SOA => [ResourceData\SOA::class, 'protocolEncode'],
        ResourceTypes::SRV => [ResourceData\SRV::class, 'protocolEncode'],
        ResourceTypes::TXT => [ResourceData\TXT::class, 'protocolEncode'],
    ];

    private static $callbackType;

    private $encoders = self::DEFAULT_ENCODERS;

    public function __construct()
    {
        if (!isset(self::$callbackType)) {
            self::$callbackType = new CallbackType(
                new ReturnType(),
                new ParameterType('context', EncodingContext::class),
                new ParameterType('record', ResourceData::class, ParameterType::COVARIANT)
            );
        }
    }

    public function registerEncoder(int $type, callable $encoder)
    {
        if (!self::$callbackType->isSatisfiedBy($encoder)) {
            throw new \LogicException(
                'Callback with signature ' . CallbackType::createFromCallable($encoder)
                . ' does not satisfy required signature ' . self::$callbackType
            );
        }

        $this->encoders[$type] = $encoder;
    }

    public function hasEncoderForType(int $type): bool
    {
        return isset($this->encoders[$type]);
    }

    public function restoreDefaultEncoder(int $type)
    {
        $this->encoders[$type] = self::DEFAULT_ENCODERS[$type] ?? null;
    }

    public function encode(Context $ctx, int $typeId, ResourceData $data)
    {
        if (isset($this->encoders[$typeId])) {
            $this->encoders[$typeId]($ctx, $data);
            return;
        }

        if (!$data instanceof RawResourceData) {
            throw new \UnexpectedValueException("Unknown resource data type: {{$typeId}}");
        }

        $ctx->appendData($data->getData());
    }
}
