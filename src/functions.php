<?php declare(strict_types = 1);

namespace DaveRandom\LibDNS;

use DaveRandom\LibDNS\Decoding\DecodingContext;
use DaveRandom\LibDNS\Encoding\EncodingContext;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPAddress;
use DaveRandom\Network\IPv4Address;
use DaveRandom\Network\IPv6Address;

const UINT16_MIN = 0;
const UINT16_MAX = 0xffff;
const UINT16_MASK = 0xffff;

const UINT32_MIN = \PHP_INT_SIZE === 8 ? 0 : \PHP_INT_MIN;
const UINT32_MAX = \PHP_INT_SIZE === 8 ? 0xffffffff : \PHP_INT_MAX;
const UINT32_MASK = (0x7fffffff << 1) | 0x01;

function normalize_name(string $label): string
{
    \trigger_error(
        'DaveRandom\LibDNS\normalize_name() is deprecated, please use DaveRandom\Network\normalize_dns_name() instead',
        E_USER_DEPRECATED
    );

    return \DaveRandom\Network\normalize_dns_name($label);
}

function encode_domain_name(EncodingContext $ctx, DomainName $name, bool $neverCompress = false)
{
    $labels = $name->getLabels();
    $result = '';

    // If compression is disabled, just encode the whole name literally
    if ($neverCompress || !$ctx->compress) {
        foreach ($labels as $label) {
            $result .= \chr(\strlen($label)) . $label;
        }

        goto done;
    }

    // Loop the labels until there are none left
    do {
        $part = \implode('.', $labels);

        // If the remainder of the name exists in the registry, encode a pointer to it and return
        if (isset($ctx->labelIndexes[$part])) {
            $ctx->appendData($result . \pack('n', 0b1100000000000000 | $ctx->labelIndexes[$part]));
            return;
        }

        // Add the remainder of the name to the registry as long as the offset is small enough to fit in a reference
        if ($ctx->offset <= 0x3fff) {
            $ctx->labelIndexes[$part] = $ctx->offset;
        }

        // Encode the current label literally
        $label = \array_shift($labels);
        $length = \strlen($label);

        $ctx->appendData(\chr($length) . $label);
    } while (!empty($labels));

    done:
    $ctx->appendData("{$result}\x00");
}

function decode_domain_name(DecodingContext $ctx): DomainName
{
    $startIndex = $ctx->offset;
    $result = [];
    $literalLabels = [];

    // Read the label length byte from the buffer
    while (0 !== $length = \ord($ctx->data[$ctx->offset++])) {
        switch ($length & 0b11000000) {

            // If the first two bits are set, the label is a pointer to somewhere earlier in the packet
            case 0b11000000: {
                $index = (($length & 0b00111111) << 8) | \ord($ctx->data[$ctx->offset++]);

                if (!isset($ctx->labelsByIndex[$index])) {
                    throw new \UnexpectedValueException(\sprintf(
                        'Decode error: Invalid compression pointer reference 0x%X in domain name at position 0x%X',
                        $index,
                        $startIndex
                    ));
                }

                $result = $ctx->labelsByIndex[$index];
                break 2;
            }

            // If the first two bits are clear, the label is $length bytes long
            case 0: {
                if ($ctx->offset + $length > \strlen($ctx->data)) {
                    throw new \UnexpectedValueException(\sprintf(
                        'Decode error: Incomplete label in domain name at position 0x%X',
                        $startIndex
                    ));
                }

                $literalLabels[] = [$ctx->offset - 1, \substr($ctx->data, $ctx->offset, $length)];
                $ctx->offset += $length;

                break;
            }

            // Handling of any other combination is not specified
            default: throw new \UnexpectedValueException(\sprintf(
                'Decode error: Invalid label type 0x%X in domain name at position 0x%X',
                $length & 0b11000000,
                $startIndex
            ));
        }

        if (!isset($ctx->data[$ctx->offset])) {
            throw new \UnexpectedValueException(\sprintf(
                'Decode error: Incomplete domain name at position 0x%X',
                $startIndex
            ));
        }
    }

    // Store decoded label indexes for later compression lookups
    while (list($index, $label) = \array_pop($literalLabels)) {
        \array_unshift($result, $label);
        $ctx->labelsByIndex[$index] = $result;
    }

    return new DomainName($result, false);
}

function encode_ipv4address(EncodingContext $ctx, IPv4Address $address)
{
    $ctx->appendData(\pack(
        'C4',
        $address->getOctet1(),
        $address->getOctet2(),
        $address->getOctet3(),
        $address->getOctet4()
    ));
}

function decode_ipv4address(DecodingContext $ctx): IPv4Address
{
    $octets = $ctx->unpack('C4', 4);

    return new IPv4Address($octets[1], $octets[2], $octets[3], $octets[4]);
}

function encode_ipv6address(EncodingContext $ctx, IPv6Address $address)
{
    $ctx->appendData(\pack(
        'n8',
        $address->getHextet1(),
        $address->getHextet2(),
        $address->getHextet3(),
        $address->getHextet4(),
        $address->getHextet5(),
        $address->getHextet6(),
        $address->getHextet7(),
        $address->getHextet8()
    ));
}

function decode_ipv6address(DecodingContext $ctx): IPv6Address
{
    $hextets = $ctx->unpack('n8', 16);

    return new IPv6Address(
        $hextets[1], $hextets[2], $hextets[3], $hextets[4], $hextets[5], $hextets[6], $hextets[7], $hextets[8]
    );
}

function encode_character_data(EncodingContext $ctx, string $data)
{
    $length = \strlen($data);

    if ($length > 255) {
        throw new \InvalidArgumentException(
            "Maximum length of character-data string is 255 bytes (got {$length} bytes)"
        );
    }

    $ctx->appendData(\chr(\strlen($data)) . $data);
}

function decode_character_data(DecodingContext $ctx): string
{
    $length = \ord($ctx->data[$ctx->offset++]);

    return $ctx->unpack("a{$length}", $length)[1];
}

function validate_nibble(string $description, int $value): int
{
    if (($value & 0x0f) !== $value) {
        throw new \InvalidArgumentException("{$description} must be in the range 0 - 15");
    }

    return $value;
}

function validate_byte(string $description, int $value): int
{
    if (($value & 0xff) !== $value) {
        throw new \InvalidArgumentException("{$description} must be in the range 0 - 255");
    }

    return $value;
}

function validate_uint16(string $description, int $value): int
{
    if (($value & 0xffff) !== $value) {
        throw new \InvalidArgumentException("{$description} must be in the range 0 - 65535");
    }

    return $value;
}

function validate_uint32(string $description, int $value): int
{
    if (($value & UINT32_MASK) !== $value) {
        throw new \InvalidArgumentException("{$description} must be in the range " . UINT32_MIN . " - " . UINT32_MAX);
    }

    return $value;
}

function ipaddress_to_ptr_name(IPAddress $address): DomainName
{
    if ($address instanceof IPv4Address) {
        return new DomainName([
            $address->getOctet4(), $address->getOctet3(), $address->getOctet2(), $address->getOctet1(),
            'in-addr', 'arpa'
        ]);
    }

    if (!$address instanceof IPv6Address) {
        throw new \InvalidArgumentException('Unknown IP address type: ' . \get_class($address));
    }

    $labels = [];
    $bin = $address->toBinary();

    for ($i = 15; $i >= 0; $i--) {
        $byte = \ord($bin[$i]);
        \array_push($labels, \dechex($byte & 0x0f), \dechex(($byte & 0xf0) >> 4));
    }

    $labels[] = 'ip6';
    $labels[] = 'arpa';

    return new DomainName($labels);
}
