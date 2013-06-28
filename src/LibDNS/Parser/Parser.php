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
    \LibDNS\DataTypes\DataTypeFactory;

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
     * Parse a domain name
     *
     * @param \LibDNS\Parser\ParsingContext $parsingContext
     *
     * @return \LibDNS\DataTypes\DomainName
     *
     * @throws \UnexpectedValueException When the domain name is invalid
     */
    private function parseDomainName(ParsingContext $parsingContext)
    {
        $packet = $parsingContext->getPacket();
        $labelRegistry = $parsingContext->getLabelRegistry();

        $labels = [];

        while ($length = ord($this->readDataFromPacket($packet, 1))) {
            if ($length === 0) {
                break;
            }

            $labelType = $length & 0b11000000;
            if ($labelType === self::LABELTYPE_LABEL) {
                $index = $packet->getPointer() - 1;
                $label = $this->readDataFromPacket($packet, $length);

                array_unshift($labels, [$index, $label]);
            } else if ($labelType === self::LABELTYPE_POINTER) {
                $index = (($length & 0b00111111) << 8) | ord($this->readDataFromPacket($packet, 1));
                $label = $labelRegistry->lookupLabel($index);
                 if ($label === null) {
                    throw new \UnexpectedValueException('Parse error: Invalid compression pointer in domain name');
                }

                array_merge($labels, $label);

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

        return $this->dataTypeFactory->createDomainName($result);
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

        return $message;
    }
}
