<?php
/**
 * Parses raw network data to Message objects
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Parser;

use \LibDNS\Packets\PacketFactory,
    \LibDNS\Packets\Packet,
    \LibDNS\Messages\MessageFactory,
    \LibDNS\Messages\Message,
    \LibDNS\Records\QuestionFactory,
    \LibDNS\Records\ResourceBuilder,
    \LibDNS\DataTypes\DataTypeFactory,
    \LibDNS\DataTypes\SimpleType,
    \LibDNS\DataTypes\ComplexType,
    \LibDNS\DataTypes\SimpleTypes,
    \LibDNS\DataTypes\Anything,
    \LibDNS\DataTypes\BitMap,
    \LibDNS\DataTypes\Char,
    \LibDNS\DataTypes\CharacterString,
    \LibDNS\DataTypes\DomainName,
    \LibDNS\DataTypes\IPv4Address,
    \LibDNS\DataTypes\IPv6Address,
    \LibDNS\DataTypes\Long,
    \LibDNS\DataTypes\Short;

/**
 * Parses raw network data to Message objects
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Parser
{
    const LABELTYPE_LABEL   = 0b00000000;
    const LABELTYPE_POINTER = 0b11000000;

    /**
     * @var \LibDNS\Packets\PacketFactory
     */
    private $packetFactory;

    /**
     * @var \LibDNS\Messages\MessageFactory
     */
    private $messageFactory;

    /**
     * @var \LibDNS\Records\QuestionFactory
     */
    private $questionFactory;

    /**
     * @var \LibDNS\Records\ResourceBuilder
     */
    private $resourceBuilder;

    /**
     * @var \LibDNS\Parser\ParsingContextFactory
     */
    private $parsingContextFactory;

    /**
     * Constructor
     *
     * @param \LibDNS\Packets\PacketFactory $packetFactory
     * @param \LibDNS\Messages\MessageFactory $messageFactory
     * @param \LibDNS\Records\QuestionFactory $questionFactory
     * @param \LibDNS\Records\ResourceBuilder $resourceBuilder
     * @param \LibDNS\DataTypes\DataTypeFactory $dataTypeFactory
     * @param \LibDNS\Parser\ParsingContextFactory $parsingContextFactory
     */
    public function __construct(
        PacketFactory $packetFactory,
        MessageFactory $messageFactory,
        QuestionFactory $questionFactory,
        ResourceBuilder $resourceBuilder,
        DataTypeFactory $dataTypeFactory,
        ParsingContextFactory $parsingContextFactory
    ) {
        $this->packetFactory = $packetFactory;
        $this->messageFactory = $messageFactory;
        $this->questionFactory = $questionFactory;
        $this->resourceBuilder = $resourceBuilder;
        $this->dataTypeFactory = $dataTypeFactory;
        $this->parsingContextFactory = $parsingContextFactory;
    }

    /**
     * Read a specified number of bytes of data from a packet
     *
     * @param \LibDNS\Packets\Packet $packet
     * @param int                    $length
     *
     * @return string
     *
     * @throws \UnexpectedValueException When the read operation does not result in the requested number of bytes
     */
    private function readDataFromPacket(Packet $packet, $length)
    {
        if ($packet->getBytesRemaining() < $length) {
            throw new \UnexpectedValueException('Parse error: Incomplete packet');
        }

        return $packet->read($length);
    }

    /**
     * Parse the header section of the message
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\Messages\Message      $message
     *
     * @throws \UnexpectedValueException When the header section is invalid
     */
    private function parseHeader(ParsingContext $parsingContext, Message $message)
    {
        $header = unpack('nid/c2meta/nqd/nan/nns/nar', $this->readDataFromPacket($parsingContext->getPacket(), 96));
        if (!$header) {
            throw new \UnexpectedValueException('Parse error: Header unpack failed');
        }

        $message->setID($header['id']);
        $message->setType(($header['meta1'] & 0b10000000) >> 8);
        $message->setOpCode(($header['meta1'] & 0b01111000) >> 3);
        $message->isAuthoritative(($header['meta1'] & 0b00000100) >> 2);
        $message->isTruncated(($header['meta1'] & 0b00000010) >> 1);
        $message->isRecusionDesired($header['meta1'] & 0b00000001);
        $message->isRecusionAvailable(($header['meta2'] & 0b10000000) >> 8);
        $message->setResponseCode($header['meta2'] & 0b00001111);

        $parsingContext->setExpectedQuestionRecords($header['qd']);
        $parsingContext->setExpectedAnswerRecords($header['an']);
        $parsingContext->setExpectedAuthorityRecords($header['qd']);
        $parsingContext->setExpectedAdditoinalRecords($header['ar']);
    }

    /**
     * Parse an Anything field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param int                           $length
     * @param \LibDNS\DataTypes\Anything    $anything       The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseAnything(ParsingContext $parsingContext, Anything $anything, $length)
    {
        $anything->setValue($this->readDataFromPacket($parsingContext->getPacket(), $length));

        return $length;
    }

    /**
     * Parse a BitMap field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param int                           $length
     * @param \LibDNS\DataTypes\BitMap      $bitMap         The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseBitMap(ParsingContext $parsingContext, BitMap $bitMap, $length)
    {
        $bitMap->setValue($this->readDataFromPacket($parsingContext->getPacket(), $length));

        return $length;
    }

    /**
     * Parse a Char field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\Char        $char           The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseChar(ParsingContext $parsingContext, Char $char)
    {
        $value = unpack('C', $this->readDataFromPacket($parsingContext->getPacket(), 1))[1];
        $char->setValue($value);

        return 1;
    }

    /**
     * Parse a CharacterString field
     *
     * @param \LibDNS\Parser\ParsingContext     $parsingContext
     * @param \LibDNS\DataTypes\CharacterString $characterString The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseCharacterString(ParsingContext $parsingContext, CharacterString $characterString)
    {
        $packet = $parsingContext->getPacket();
        $length = ord($this->readDataFromPacket($packet, 1));
        $characterString->setValue($this->readDataFromPacket($packet, $length));

        return $length + 1;
    }

    /**
     * Parse a DomainName field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\DomainName  $domainName     The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseDomainName(ParsingContext $parsingContext, DomainName $domainName)
    {
        $packet = $parsingContext->getPacket();
        $labelRegistry = $parsingContext->getLabelRegistry();

        $labels = [];
        $totalLength = 0;

        while ($length = ord($this->readDataFromPacket($packet, 1))) {
            $totalLength++;

            if ($length === 0) {
                break;
            }

            $labelType = $length & 0b11000000;
            if ($labelType === self::LABELTYPE_LABEL) {
                $index = $packet->getPointer() - 1;
                $label = $this->readDataFromPacket($packet, $length);

                array_unshift($labels, [$index, $label]);
                $totalLength += $length;
            } else if ($labelType === self::LABELTYPE_POINTER) {
                $index = (($length & 0b00111111) << 8) | ord($this->readDataFromPacket($packet, 1));
                $ref = $labelRegistry->lookupLabel($index);
                if ($ref === null) {
                    throw new \UnexpectedValueException('Parse error: Invalid compression pointer reference in domain name');
                }

                array_unshift($labels, $ref);
                $totalLength++;

                break;
            } else {
                throw new \UnexpectedValueException('Parse error: Invalid label type in domain name');
            }
        }

        $result = [];
        foreach ($labels as $label) {
            if (is_int($label[0])) {
                array_unshift($result, $label[1]);
                $labelRegistry->register($result, $label[0]);
            } else {
                $result = $label;
            }
        }
        $domainName->setValue($result);

        return $totalLength;
    }

    /**
     * Parse an IPv4Address field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\IPv4Address $ipv4Address    The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseIPv4Address(ParsingContext $parsingContext, IPv4Address $ipv4Address)
    {
        $octets = unpack('C4', $this->readDataFromPacket($parsingContext->getPacket(), 4));
        $ipv4Address->setOctets($octets);

        return 4;
    }

    /**
     * Parse an IPv6Address field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\IPv6Address $ipv6Address    The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseIPv6Address(ParsingContext $parsingContext, IPv6Address $ipv6Address)
    {
        $shorts = unpack('n8', $this->readDataFromPacket($parsingContext->getPacket(), 16));
        $ipv6Address->setShorts($shorts);

        return 16;
    }

    /**
     * Parse a Long field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\Long        $long           The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseLong(ParsingContext $parsingContext, Long $long)
    {
        $value = unpack('N', $this->readDataFromPacket($parsingContext->getPacket(), 4))[1];
        $long->setValue($value);
    }

    /**
     * Parse a Short field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\Short       $short          The object to populate with the result
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     */
    private function parseShort(ParsingContext $parsingContext, Short $short)
    {
        $value = unpack('n', $this->readDataFromPacket($parsingContext->getPacket(), 2))[1];
        $short->setValue($value);
    }

    /**
     * Parse a SimpleType field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\SimpleType  $simpleType     The object to populate with the result
     * @param int                           $length         Expected data length
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     * @throws \InvalidArgumentException When the SimpleType subtype is unknown
     */
    private function parseSimpleType(ParsingContext $parsingContext, SimpleType $simpleType, $length)
    {
        if ($simpleType instanceof Anything) {
            $result = $this->parseAnything($parsingContext, $simpleType, $length);
        } else if ($simpleType instanceof BitMap) {
            $result = $this->parseBitMap($parsingContext, $simpleType, $length);
        } else if ($simpleType instanceof Char) {
            $result = $this->parseChar($parsingContext, $simpleType);
        } else if ($simpleType instanceof CharacterString) {
            $result = $this->parseCharacterString($parsingContext, $simpleType);
        } else if ($simpleType instanceof DomainName) {
            $result = $this->parseDomainName($parsingContext, $simpleType);
        } else if ($simpleType instanceof IPv4Address) {
            $result = $this->parseIPv4Address($parsingContext, $simpleType);
        } else if ($simpleType instanceof IPv6Address) {
            $result = $this->parseIPv6Address($parsingContext, $simpleType);
        } else if ($simpleType instanceof Long) {
            $result = $this->parseLong($parsingContext, $simpleType);
        } else if ($simpleType instanceof Short) {
            $result = $this->parseShort($parsingContext, $simpleType);
        } else {
            throw new \InvalidArgumentException('Unknown SimpleType ' . get_class($simpleType));
        }

        return $result;
    }

    /**
     * Parse a ComplexType field
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     * @param \LibDNS\DataTypes\ComplexType $complexType    The object to populate with the result
     * @param int                           $length         Expected data length
     *
     * @return int The number of packet bytes consumed by the operation
     *
     * @throws \UnexpectedValueException When the packet data is invalid
     * @throws \InvalidArgumentException When a SimpleType subtype is unknown
     */
    private function parseComplexType(ParsingContext $parsingContext, ComplexType $complexType, $length)
    {   
    }

    /**
     * Parse a question record
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     *
     * @return \LibDNS\Records\Question
     *
     * @throws \UnexpectedValueException When the record is invalid
     */
    private function parseQuestionRecord(ParsingContext $parsingContext)
    {
        $domainName = $this->dataTypeFactory->createDomainName();
        $this->parseDomainName($parsingContext, $domainName);
        $meta = unpack('ntype/nclass', $this->readDataFromPacket($parsingContext->getPacket(), 4));

        $question = $this->questionFactory->create($meta['type']);
        $question->setName($domainName);
        $question->setClass($meta['class']);

        return $question;
    }

    /**
     * Parse a resource record
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     *
     * @return \LibDNS\Records\Resource
     *
     * @throws \UnexpectedValueException When the record is invalid
     */
    private function parseResourceRecord(ParsingContext $parsingContext)
    {
        $domainName = $this->dataTypeFactory->createDomainName();
        $this->parseDomainName($parsingContext, $domainName);
        $meta = unpack('ntype/nclass/Nttl/nlength', $this->readDataFromPacket($parsingContext->getPacket(), 10));

        $resource = $this->resourceBuilder->build($meta['type']);
        $resource->setName($domainName);
        $resource->setClass($meta['class']);
        $resource->setTTL($meta['ttl']);

        $data = $resource->getData();
        if ($data instanceof SimpleType) {
            $this->parseSimpleType($parsingContext, $data, $meta['length']);
        } else if ($data instanceof ComplexType) {
            $this->parseComplexType($parsingContext, $data, $meta['length']);
        } else {
            throw new \InvalidArgumentException('Unknown data type ' . get_class($simpleType));
        }

        return $question;
    }

    /**
     * Parse a Message from raw network data
     *
     * @param string $data The data string to parse
     *
     * @return \LibDNS\Messages\Message
     */
    public function parse($data)
    {
        $packet = $this->packetFactory->create($data);
        $parsingContext = $this->parsingContextFactory->create($packet);
        $message = $this->messageFactory->create();

        $this->parseHeader($parsingContext, $message);

        $questionRecords = $message->getQuestionRecords();
        $expected = $parsingContext->getExpectedQuestionRecords();
        for ($i = 0; $i < $expected; $i++) {
            $questionRecords->add($this->parseQuestionRecord($parsingContext));
        }

        $answerRecords = $message->getAnswerRecords();
        $expected = $parsingContext->getExpectedAnswerRecords();
        for ($i = 0; $i < $expected; $i++) {
            $answerRecords->add($this->parseResourceRecord($parsingContext));
        }

        $authorityRecords = $message->getAuthorityRecords();
        $expected = $parsingContext->getExpectedAuthorityRecords();
        for ($i = 0; $i < $expected; $i++) {
            $authorityRecords->add($this->parseResourceRecord($parsingContext));
        }

        $addtionalRecords = $message->getAddtionalRecords();
        $expected = $parsingContext->getExpectedAddtionalRecords();
        for ($i = 0; $i < $expected; $i++) {
            $addtionalRecords->add($this->parseResourceRecord($parsingContext));
        }

        if ($packet->getBytesRemaining() !== 0) {
            throw new \UnexpectedValueException('Parse error: Unexpected data at end of packet');
        }

        return $message;
    }
}
