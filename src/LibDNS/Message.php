<?php
/**
 * Represents a DNS protocol message
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    LibDNS
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS;

/**
 * Represents a DNS protocol message
 *
 * @category   LibDNS
 * @package    LibDNS
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Message
{
    /**
     * @var int Unsigned short that identifies the DNS transaction
     */
    private $id

    /**
     * @var int Indicates the type of the message, can be indicated using the MessageTypes enum
     */
    private $type;

    /**
     * @var int Message opcode, can be indicated using the MessageOpCodes enum
     */
    private $opCode;

    /**
     * @var bool Whether a response message is authoritative
     */
    private $authoritative;

    /**
     * @var bool Whether the message is truncated
     */
    private $truncated;

    /**
     * @var bool Whether a query desires the server to recurse the lookup
     */
    private $recusionDesired = true;

    /**
     * @var bool Whether a server could provide recursion in a response
     */
    private $recusionAvailable = false;

    /**
     * @var int Message response code, can be indicated using the MessageResponseCodes enum
     */
    private $responseCode;

    /**
     * @var RecordCollection Collection of question records
     */
    private $questions;

    /**
     * @var RecordCollection Collection of question records
     */
    private $answerRecords;

    /**
     * @var RecordCollection Collection of authority records
     */
    private $authorityRecords;

    /**
     * @var RecordCollection Collection of authority records
     */
    private $additionalRecords;

    /**
     * Get the value of the message ID field
     *
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Set the value of the message ID field
     *
     * @param int $id The new value
     *
     * @throws \RangeException When the supplied value is outside the valid range 0 - 65535
     */
    public function setID($id)
    {
        $id = (int) $id;
        if ($id < 0 || $id > 65535) {
            throw new \RangeException('Message ID must be in the range 0 - 65535');
        }

        $this->id = $id;
    }

    /**
     * Get the value of the message type field
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of the message type field
     *
     * @param int $type The new value
     *
     * @throws \RangeException When the supplied value is outside the valid range 0 - 1
     */
    public function setType($type)
    {
        $type = (int) $type;
        if ($type < 0 || $type > 1) {
            throw new \RangeException('Message type must be in the range 0 - 1');
        }

        $this->type = $type;
    }

    /**
     * Get the value of the message opcode field
     *
     * @return int
     */
    public function getOpCode()
    {
        return $this->opCode;
    }

    /**
     * Set the value of the message opcode field
     *
     * @param int $opCode The new value
     *
     * @throws \RangeException When the supplied value is outside the valid range 0 - 15
     */
    public function setOpCode($opCode)
    {
        $opCode = (int) $opCode;
        if ($opCode < 0 || $opCode > 15) {
            throw new \RangeException('Message opcode must be in the range 0 - 15');
        }

        $this->opCode = $opCode;
    }

    /**
     * Inspect the value of the authoritative field and optionally set a new value
     *
     * @param bool $newValue The new value
     *
     * @return bool The old value
     */
    public function isAuthoritative($newValue = null)
    {
        $result = $this->authoritative;

        if ($newValue !== null) {
            $this->authoritative = (bool) $newValue;
        }

        return (bool) $result;
    }

    /**
     * Inspect the value of the truncated field and optionally set a new value
     *
     * @param bool $newValue The new value
     *
     * @return bool The old value
     */
    public function isTruncated($newValue = null)
    {
        $result = $this->truncated;

        if ($newValue !== null) {
            $this->truncated = (bool) $newValue;
        }

        return (bool) $result;
    }

    /**
     * Inspect the value of the recusion desired field and optionally set a new value
     *
     * @param bool $newValue The new value
     *
     * @return bool The old value
     */
    public function isRecusionDesired($newValue = null)
    {
        $result = $this->recusionDesired;

        if ($newValue !== null) {
            $this->recusionDesired = (bool) $newValue;
        }

        return (bool) $result;
    }

    /**
     * Inspect the value of the recusion available field and optionally set a new value
     *
     * @param bool $newValue The new value
     *
     * @return bool The old value
     */
    public function isRecusionAvailable($newValue = null)
    {
        $result = $this->recusionAvailable;

        if ($newValue !== null) {
            $this->recusionAvailable = (bool) $newValue;
        }

        return (bool) $result;
    }

    /**
     * Get the value of the message response code field
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->opCode;
    }

    /**
     * Set the value of the message response code field
     *
     * @param int $responseCode The new value
     *
     * @throws \RangeException When the supplied value is outside the valid range 0 - 15
     */
    public function setResponseCode($responseCode)
    {
        $responseCode = (int) $responseCode;
        if ($responseCode < 0 || $responseCode > 15) {
            throw new \RangeException('Message response code must be in the range 0 - 15');
        }

        $this->responseCode = $responseCode;
    }
}
