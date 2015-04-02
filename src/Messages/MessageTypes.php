<?php
/**
 * Enumeration of possible message types
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Messages
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS\Messages;

use \LibDNS\Enumeration;

/**
 * Enumeration of possible message types
 *
 * @category LibDNS
 * @package Messages
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class MessageTypes extends Enumeration
{
    const QUERY = 0;
    const RESPONSE = 1;
}
