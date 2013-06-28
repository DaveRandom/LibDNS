<?php
/**
 * Enumeration of simple data types
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\DataTypes;

use \LibDNS\Enumeration;

/**
 * Enumeration of simple data types
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class SimpleTypes extends Enumeration
{
    const ANYTHING         = 1;
    const BITMAP           = 2;
    const CHAR             = 3;
    const CHARACTER_STRING = 4;
    const DOMAIN_NAME      = 5;
    const IPV4_ADDRESS     = 6;
    const IPV6_ADDRESS     = 7;
    const LONG             = 8;
    const SHORT            = 9;
}
