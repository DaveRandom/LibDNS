<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;

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

    public function getTypeId(): int
    {
        return ResourceTypes::TXT;
    }

    public static function decode(DecodingContext $ctx, int $length): TXT
    {
        $consumed = 0;
        $strings = [];

        while ($consumed < $length) {
            $string = \DaveRandom\LibDNS\decode_character_data($ctx);
            $strings[] = $string;
            $consumed += \strlen($string) + 1;
        }

        return new TXT($strings);
    }

    public static function encode(EncodingContext $ctx, TXT $data)
    {
        foreach ($data->getStrings() as $string) {
            \DaveRandom\LibDNS\encode_character_data($ctx, $string);
        }
    }
}
