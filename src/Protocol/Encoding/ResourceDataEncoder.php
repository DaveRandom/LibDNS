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
        ResourceTypes::A => [ResourceData\A::class, 'encode'],
        ResourceTypes::AAAA => [ResourceData\AAAA::class, 'encode'],
        ResourceTypes::CNAME => [ResourceData\CNAME::class, 'encode'],
        ResourceTypes::CNAME => [ResourceData\DNAME::class, 'encode'],
        ResourceTypes::MX => [ResourceData\MX::class, 'encode'],
        ResourceTypes::NAPTR => [ResourceData\NAPTR::class, 'encode'],
        ResourceTypes::NS => [ResourceData\NS::class, 'encode'],
        ResourceTypes::PTR => [ResourceData\PTR::class, 'encode'],
        ResourceTypes::RP => [ResourceData\RP::class, 'encode'],
        ResourceTypes::SOA => [ResourceData\SOA::class, 'encode'],
        ResourceTypes::SRV => [ResourceData\SRV::class, 'encode'],
        ResourceTypes::TXT => [ResourceData\TXT::class, 'encode'],
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
