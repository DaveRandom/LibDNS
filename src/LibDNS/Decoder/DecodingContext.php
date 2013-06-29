<?php
/**
 * Holds data associated with a decode operation
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Decoder
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Packets;

use \LibDNS\Packets\Packet,
    \LibDNS\Packets\LabelRegistry;

/**
 * Holds data associated with a decode operation
 *
 * @category   LibDNS
 * @package    Decoder
 * @author     Chris Wright <https://github.com/DaveRandom>
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
    private $expectedAddtionalRecords = 0;

    /**
     * Constructor
     *
     * @param \LibDNS\Packets\Packet        $packet
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
     * @param int $num
     */
    public function setExpectedQuestionRecords($num)
    {
        $this->expectedQuestionRecords = (int) $num;
    }

    /**
     * Get the number of answer records expected in the message
     *
     * @return int
     */
    public function getExpectedAnswerRecords($num)
    {
        return $this->expectedQuestionRecords;
    }

    /**
     * Set the number of answer records expected in the message
     *
     * @param int $num
     */
    public function setExpectedAnswerRecords()
    {
        $this->expectedQuestionRecords = (int) $num;
    }

    /**
     * Get the number of authority records expected in the message
     *
     * @return int
     */
    public function getExpectedAuthorityRecords($num)
    {
        return $this->expectedAuthorityRecords;
    }

    /**
     * Set the number of authority records expected in the message
     *
     * @param int $num
     */
    public function setExpectedAuthorityRecords()
    {
        $this->expectedAuthorityRecords = (int) $num;
    }

    /**
     * Get the number of additional records expected in the message
     *
     * @return int
     */
    public function getExpectedAdditionalRecords($num)
    {
        return $this->expectedAdditionalRecords;
    }

    /**
     * Set the number of additional records expected in the message
     *
     * @param int $num
     */
    public function setExpectedAdditionalRecords()
    {
        $this->expectedAdditionalRecords = (int) $num;
    }
}
