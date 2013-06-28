<?php
/**
 * Creates ParsingContext objects
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

use \LibDNS\Packets\Packet,
    \LibDNS\Packets\LabelRegistry;

/**
 * Creates ParsingContext objects
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ParsingContextFactory
{
    /**
     * Create a new ParsingContext object
     *
     * @param \LibDNS\Packets\Packet $packet The packet to be parsed
     *
     * @return \LibDNS\Packets\ParsingContext
     */
    public function create(Packet $packet)
    {
        return new ParsingContext($packet, new LabelRegistry);
    }
}
