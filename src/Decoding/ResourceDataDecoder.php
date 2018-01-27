<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Decoding;

use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;

final class ResourceDataDecoder
{
    const DEFAULT_DECODERS = [
        ResourceTypes::A => [ResourceData\A::class, 'decode'],
        ResourceTypes::AAAA => [ResourceData\AAAA::class, 'decode'],
        ResourceTypes::CNAME => [ResourceData\CNAME::class, 'decode'],
        ResourceTypes::DNAME => [ResourceData\DNAME::class, 'decode'],
        ResourceTypes::MX => [ResourceData\MX::class, 'decode'],
        ResourceTypes::NS => [ResourceData\NS::class, 'decode'],
        ResourceTypes::PTR => [ResourceData\PTR::class, 'decode'],
        ResourceTypes::SOA => [ResourceData\SOA::class, 'decode'],
        ResourceTypes::TXT => [ResourceData\TXT::class, 'decode'],
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
            return new ResourceData\UnknownResourceData($type, $ctx->unpack("a{$length}", $length)[1]);
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
