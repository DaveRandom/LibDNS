<?php
/**
 * Encodes Message objects to raw network data
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Encoder
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Encoder;

use \LibDNS\Packets\PacketFactory,
    \LibDNS\Messages\Message,
    \LibDNS\Records\Question,
    \LibDNS\Records\Resource,
    \LibDNS\Records\Types\Type,
    \LibDNS\Records\Types\Anything,
    \LibDNS\Records\Types\BitMap,
    \LibDNS\Records\Types\Char,
    \LibDNS\Records\Types\CharacterString,
    \LibDNS\Records\Types\DomainName,
    \LibDNS\Records\Types\IPv4Address,
    \LibDNS\Records\Types\IPv6Address,
    \LibDNS\Records\Types\Long,
    \LibDNS\Records\Types\Short,
    \LibDNS\Records\Types\Types;

/**
 * Encodes Message objects to raw network data
 *
 * @category   LibDNS
 * @package    Encoder
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Encoder
{
    /**
     * @var \LibDNS\Packets\PacketFactory
     */
    private $packetFactory;

    /**
     * @var \LibDNS\Encoder\EncodingContextFactory
     */
    private $encodingContextFactory;

    /**
     * Constructor
     *
     * @param \LibDNS\Packets\PacketFactory $packetFactory
     * @param \LibDNS\Encoder\EncodingContextFactory $encodingContextFactory
     */
    public function __construct(PacketFactory $packetFactory, EncodingContextFactory $encodingContextFactory)
    {
        $this->packetFactory = $packetFactory;
        $this->encodingContextFactory = $encodingContextFactory;
    }

    /**
     * Encode the header section of the message
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Messages\Message        $message
     *
     * @throws \UnexpectedValueException When the header section is invalid
     */
    private function encodeHeader(EncodingContext $encodingContext, Message $message)
    {
        $header = [
            'id' => $message->getID(),
            'meta' => 0,
            'qd' => $message->getQuestionRecords()->count(),
            'an' => $message->getAnswerRecords()->count(),
            'ns' => $message->getAuthorityRecords()->count(),
            'ar' => $message->getAdditionalRecords()->count()
        ];

        $header['meta'] |= $message->getType() << 16;
        $header['meta'] |= $message->getOpCode() << 11;
        $header['meta'] |= ((int) $message->isAuthoritative()) << 10;
        $header['meta'] |= ((int) $encodingContext->isTruncated()) << 9;
        $header['meta'] |= ((int) $message->isRecursionDesired()) << 8;
        $header['meta'] |= ((int) $message->isRecursionAvailable()) << 7;
        $header['meta'] |= $message->getResponseCode();

        return pack('n*', $header['id'], $header['meta'], $header['qd'], $header['an'], $header['ns'], $header['ar']);
    }

    /**
     * Encode an Anything field
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Types\Anything  $anything
     *
     * @return string
     */
    private function encodeAnything(EncodingContext $encodingContext, Anything $anything)
    {
        return $anything->getValue();
    }

    /**
     * Encode a BitMap field
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Types\BitMap    $bitMap
     *
     * @return string
     */
    private function encodeBitMap(EncodingContext $encodingContext, BitMap $bitMap)
    {
        return $bitMap->getValue();
    }

    /**
     * Encode a Char field
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Types\Char      $char
     *
     * @return string
     */
    private function encodeChar(EncodingContext $encodingContext, Char $char)
    {
        return chr($char->getValue());
    }

    /**
     * Encode a CharacterString field
     *
     * @param \LibDNS\Encoder\EncodingContext       $encodingContext
     * @param \LibDNS\Records\Types\CharacterString $characterString
     *
     * @return string
     */
    private function encodeCharacterString(EncodingContext $encodingContext, CharacterString $characterString)
    {
        $data = $characterString->getValue();
        return chr(strlen($data)) . $data;
    }

    /**
     * Encode a DomainName field
     *
     * @param \LibDNS\Encoder\EncodingContext  $encodingContext
     * @param \LibDNS\Records\Types\DomainName $domainName
     *
     * @return string
     */
    private function encodeDomainName(EncodingContext $encodingContext, DomainName $domainName)
    {
        $packetIndex = $encodingContext->getPacket()->getLength() + 12;
        $labelRegistry = $encodingContext->getLabelRegistry();

        $result = '';
        $labels = $domainName->getLabels();

        if ($encodingContext->useCompression()) {
            do {
                $part = implode('.', $labels);
                $index = $labelRegistry->lookupIndex($part);

                if ($index === null) {
                    $labelRegistry->register($part, $packetIndex);

                    $label = array_shift($labels);
                    $length = strlen($label);

                    $result .= chr($length) . $label;
                    $packetIndex += $length + 1;
                } else {
                    $result .= pack('n', 0b1100000000000000 | $index);
                    break;
                }
            } while($labels);

            if (!$labels) {
                $result .= "\x00";
            }
        } else {
            foreach ($labels as $label) {
                $result .= chr(strlen($label)) . $label;
            }

            $result .= "\x00";
        }

        return $result;
    }

    /**
     * Encode an IPv4Address field
     *
     * @param \LibDNS\Encoder\EncodingContext   $encodingContext
     * @param \LibDNS\Records\Types\IPv4Address $ipv4Address
     *
     * @return string
     */
    private function encodeIPv4Address(EncodingContext $encodingContext, IPv4Address $ipv4Address)
    {
        $octets = $ipv4Address->getOctets();
        return pack('C*', $octets[0], $octets[1], $octets[2], $octets[3]);
    }

    /**
     * Encode an IPv6Address field
     *
     * @param \LibDNS\Encoder\EncodingContext   $encodingContext
     * @param \LibDNS\Records\Types\IPv6Address $ipv6Address
     *
     * @return string
     */
    private function encodeIPv6Address(EncodingContext $encodingContext, IPv6Address $ipv6Address)
    {
        $shorts = $ipv6Address->getShorts();
        return pack('n*', $shorts[0], $shorts[1], $shorts[2], $shorts[3], $shorts[4], $shorts[5], $shorts[6], $shorts[7]);
    }

    /**
     * Encode a Long field
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Types\Long      $long
     *
     * @return string
     */
    private function encodeLong(EncodingContext $encodingContext, Long $long)
    {
        return pack('N', $long->getValue());
    }

    /**
     * Encode a Short field
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Types\Short     $short
     *
     * @return string
     */
    private function encodeShort(EncodingContext $encodingContext, Short $short)
    {
        return pack('n', $short->getValue());
    }

    /**
     * Encode a type object
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Types\Type      $type
     *
     * @return string
     */
    private function encodeType(EncodingContext $encodingContext, Type $type)
    {
        if ($type instanceof Anything) {
            $result = $this->encodeAnything($encodingContext, $type);
        } else if ($type instanceof BitMap) {
            $result = $this->encodeBitMap($encodingContext, $type);
        } else if ($type instanceof Char) {
            $result = $this->encodeChar($encodingContext, $type);
        } else if ($type instanceof CharacterString) {
            $result = $this->encodeCharacterString($encodingContext, $type);
        } else if ($type instanceof DomainName) {
            $result = $this->encodeDomainName($encodingContext, $type);
        } else if ($type instanceof IPv4Address) {
            $result = $this->encodeIPv4Address($encodingContext, $type);
        } else if ($type instanceof IPv6Address) {
            $result = $this->encodeIPv6Address($encodingContext, $type);
        } else if ($type instanceof Long) {
            $result = $this->encodeLong($encodingContext, $type);
        } else if ($type instanceof Short) {
            $result = $this->encodeShort($encodingContext, $type);
        } else {
            throw new \InvalidArgumentException('Unknown Type ' . get_class($type));
        }

        return $result;
    }

    /**
     * Encode a question record
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Question        $record
     */
    private function encodeQuestionRecord(EncodingContext $encodingContext, Question $record)
    {
        if (!$encodingContext->isTruncated()) {
            $packet = $encodingContext->getPacket();
            $name = $this->encodeDomainName($encodingContext, $record->getName());
            $meta = pack('n*', $record->getType(), $record->getClass());

            if (12 + $packet->getLength() + strlen($name) + 4 > 512) {
                $encodingContext->isTruncated(true);
            } else {
                $packet->write($name);
                $packet->write($meta);
            }
        }
    }

    /**
     * Encode a resource record
     *
     * @param \LibDNS\Encoder\EncodingContext $encodingContext
     * @param \LibDNS\Records\Resource        $record
     */
    private function encodeResourceRecord(EncodingContext $encodingContext, Resource $record)
    {
        if (!$encodingContext->isTruncated()) {
            $packet = $encodingContext->getPacket();
            $name = $this->encodeDomainName($encodingContext, $record->getName());

            $data = '';
            foreach ($record->getData() as $field) {
                $data .= $this->encodeType($encodingContext, $field);
            }

            $meta = pack('n2Nn', $record->getType(), $record->getClass(), $record->getTTL(), strlen($data));

            if (12 + $packet->getLength() + strlen($name) + 10 + strlen($data) > 512) {
                $encodingContext->isTruncated(true);
            } else {
                $packet->write($name);
                $packet->write($meta);
                $packet->write($data);
            }
        }
    }

    /**
     * Encode a Message to raw network data
     *
     * @param \LibDNS\Messages\Message $message  The Message to encode
     * @param bool                     $compress Enable message compression
     *
     * @return string
     */
    public function encode(Message $message, $compress = true)
    {
        $packet = $this->packetFactory->create();
        $encodingContext = $this->encodingContextFactory->create($packet, $compress);

        foreach ($message->getQuestionRecords() as $record) {
            $this->encodeQuestionRecord($encodingContext, $record);
        }
        foreach ($message->getAnswerRecords() as $record) {
            $this->encodeResourceRecord($encodingContext, $record);
        }
        foreach ($message->getAuthorityRecords() as $record) {
            $this->encodeResourceRecord($encodingContext, $record);
        }
        foreach ($message->getAdditionalRecords() as $record) {
            $this->encodeResourceRecord($encodingContext, $record);
        }

        return $this->encodeHeader($encodingContext, $message) . $packet->read($packet->getLength());
    }
}
