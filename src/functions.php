<?php declare(strict_types = 1);

namespace DaveRandom\LibDNS;

use DaveRandom\LibDNS\Decoding\DecodingContext;
use DaveRandom\LibDNS\Encoding\EncodingContext;
use DaveRandom\LibDNS\Messages\Message;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPv4Address;

const UINT32_MASK = (0x7fffffff << 1) | 0x01;
const UINT16_MASK = 0xffff;

function normalize_name(string $label): string
{
    \trigger_error(
        'DaveRandom\LibDNS\normalize_name() is deprecated, please use DaveRandom\Network\normalize_dns_name() instead',
        E_USER_DEPRECATED
    );

    return \DaveRandom\Network\normalize_dns_name($label);
}

function encode_domain_name(DomainName $name, EncodingContext $ctx, bool $neverCompress = false): string
{
    $offset = \strlen($ctx->data) + Message::HEADER_SIZE;
    $labels = $name->getLabels();
    $result = '';

    // If compression is disabled, just encode the whole name literally
    if ($neverCompress || !$ctx->compress) {
        foreach ($labels as $label) {
            $result .= \chr(\strlen($label)) . $label;
        }

        return "{$result}\x00";
    }

    // Loop the labels until there are none left
    do {
        $part = \implode('.', $labels);

        // If the remainder of the name exists in the registry, encode a pointer to it and return
        if (isset($ctx->labelIndexes[$part])) {
            return \pack('a*n', $result, 0b1100000000000000 | $ctx->labelIndexes[$part]);
        }

        // Add the remainder of the name to the registry
        $ctx->labelIndexes[$part] = $offset;

        // Encode the current label literally
        $label = \array_shift($labels);
        $length = \strlen($label);
        $result .= \chr($length) . $label;
        $offset += $length + 1;
    } while (!empty($labels));

    return "{$result}\x00";
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

function encode_ipv4address(IPv4Address $address): string
{
    return \pack('C4', $address->getOctet1(), $address->getOctet2(), $address->getOctet3(), $address->getOctet4());
}

function decode_ipv4address(DecodingContext $ctx): IPv4Address
{
    $octets = $ctx->unpack('C4', 4);

    return new IPv4Address($octets[1], $octets[2], $octets[3], $octets[4]);
}
