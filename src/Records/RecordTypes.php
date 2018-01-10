<?php declare(strict_types=1);
/**
 * Enumeration of possible record types
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace DaveRandom\LibDNS\Records;

use DaveRandom\Enum\Enum;

/**
 * Enumeration of possible record types
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
final class RecordTypes extends Enum
{
    const QUESTION = 0;
    const RESOURCE = 1;
}
