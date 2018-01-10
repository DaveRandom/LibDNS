<?php declare(strict_types=1);
/**
 * Holds data associated with an encode operation
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Encoder
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace DaveRandom\LibDNS\Encoder;

use DaveRandom\LibDNS\Packets\Packet;
use DaveRandom\LibDNS\Packets\LabelRegistry;

/**
 * Holds data associated with an encode operation
 *
 * @category LibDNS
 * @package Encoder
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class EncodingContext
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
     * @var bool
     */
    public $compress;

    /**
     * @var bool
     */
    public $isTruncated = false;

    /**
     * Constructor
     *
     * @param Packet $packet
     * @param bool $compress
     */
    public function __construct(Packet $packet, bool $compress)
    {
        $this->packet = $packet;
        $this->compress = $compress;

        $this->labelRegistry = new LabelRegistry();
    }
}
