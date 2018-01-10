<?php declare(strict_types=1);
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
namespace DaveRandom\LibDNS\Messages;

use DaveRandom\Enum\Enum;

/**
 * Enumeration of possible message types
 *
 * @category LibDNS
 * @package Messages
 * @author Chris Wright <https://github.com/DaveRandom>
 */
final class MessageTypes extends Enum
{
    const QUERY = 0;
    const RESPONSE = 1;
}
