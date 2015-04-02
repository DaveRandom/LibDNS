<?php
/**
 * Holds data associated with a decode operation
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Decoder
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS\Decoder;

use \LibDNS\Packets\Packet;
use \LibDNS\Packets\LabelRegistry;

/**
 * Holds data associated with a decode operation
 *
 * @category LibDNS
 * @package Decoder
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class DecodingContext
{
    /**
     * @var \LibDNS\Packets\Packet
     */
    private $packet;

    /**
     * @var \LibDNS\Packets\LabelRegistry
     */
    private $labelRegistry;

    /**
     * @var int
     */
    private $expectedQuestionRecords = 0;

    /**
     * @var int
     */
    private $expectedAnswerRecords = 0;

    /**
     * @var int
     */
    private $expectedAuthorityRecords = 0;

    /**
     * @var int
     */
    private $expectedAdditionalRecords = 0;

    /**
     * Constructor
     *
     * @param \LibDNS\Packets\Packet $packet
     * @param \LibDNS\Packets\LabelRegistry $labelRegistry
     */
    public function __construct(Packet $packet, LabelRegistry $labelRegistry)
    {
        $this->packet = $packet;
        $this->labelRegistry = $labelRegistry;
    }

    /**
     * Get the packet
     *
     * @return \LibDNS\Packets\Packet
     */
    public function getPacket()
    {
        return $this->packet;
    }

    /**
     * Get the label registry
     *
     * @return \LibDNS\Packets\LabelRegistry
     */
    public function getLabelRegistry()
    {
        return $this->labelRegistry;
    }

    /**
     * Get the number of question records expected in the message
     *
     * @return int
     */
    public function getExpectedQuestionRecords()
    {
        return $this->expectedQuestionRecords;
    }

    /**
     * Get the number of question records expected in the message
     *
     * @param int $expectedQuestionRecords
     */
    public function setExpectedQuestionRecords($expectedQuestionRecords)
    {
        $this->expectedQuestionRecords = (int) $expectedQuestionRecords;
    }

    /**
     * Get the number of answer records expected in the message
     *
     * @return int
     */
    public function getExpectedAnswerRecords()
    {
        return $this->expectedAnswerRecords;
    }

    /**
     * Set the number of answer records expected in the message
     *
     * @param int $expectedAnswerRecords
     */
    public function setExpectedAnswerRecords($expectedAnswerRecords)
    {
        $this->expectedAnswerRecords = (int) $expectedAnswerRecords;
    }

    /**
     * Get the number of authority records expected in the message
     *
     * @return int
     */
    public function getExpectedAuthorityRecords()
    {
        return $this->expectedAuthorityRecords;
    }

    /**
     * Set the number of authority records expected in the message
     *
     * @param int $expectedAuthorityRecords
     */
    public function setExpectedAuthorityRecords($expectedAuthorityRecords)
    {
        $this->expectedAuthorityRecords = (int) $expectedAuthorityRecords;
    }

    /**
     * Get the number of additional records expected in the message
     *
     * @return int
     */
    public function getExpectedAdditionalRecords()
    {
        return $this->expectedAdditionalRecords;
    }

    /**
     * Set the number of additional records expected in the message
     *
     * @param int $expectedAdditionalRecords
     */
    public function setExpectedAdditionalRecords($expectedAdditionalRecords)
    {
        $this->expectedAdditionalRecords = (int) $expectedAdditionalRecords;
    }
}
