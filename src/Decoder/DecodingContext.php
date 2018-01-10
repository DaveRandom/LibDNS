<?php declare(strict_types=1);
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
namespace DaveRandom\LibDNS\Decoder;

use DaveRandom\LibDNS\Packets\Packet;
use DaveRandom\LibDNS\Packets\LabelRegistry;

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
     * @var Packet
     */
    public $packet;

    /**
     * @var LabelRegistry
     */
    public $labelRegistry;

    /**
     * @var int
     */
    public $expectedQuestionRecords = 0;

    /**
     * @var int
     */
    public $expectedAnswerRecords = 0;

    /**
     * @var int
     */
    public $expectedAuthorityRecords = 0;

    /**
     * @var int
     */
    public $expectedAdditionalRecords = 0;

    /**
     * Constructor
     *
     * @param Packet $packet
     */
    public function __construct(Packet $packet)
    {
        $this->packet = $packet;

        $this->labelRegistry = new LabelRegistry();
    }
}
