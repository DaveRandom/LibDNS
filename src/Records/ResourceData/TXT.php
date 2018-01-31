<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;

final class TXT implements ResourceData
{
    private $strings;

    /**
     * @param string[] $strings
     */
    public function __construct(array $strings)
    {
        $this->strings = $strings;
    }

    /**
     * @return string[]
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return \implode(' ', $record->strings);
    }

    public static function protocolDecode(DecodingContext $ctx, int $length): self
    {
        $consumed = 0;
        $strings = [];

        while ($consumed < $length) {
            $string = \DaveRandom\LibDNS\decode_character_string($ctx);
            $strings[] = $string;
            $consumed += \strlen($string) + 1;
        }

        return new self($strings);
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        foreach ($record->getStrings() as $string) {
            \DaveRandom\LibDNS\encode_character_string($ctx, $string);
        }
    }
}
