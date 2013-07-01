<?php
/**
 * Enumeration of possible resource TYPE values
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Records;

use \LibDNS\Enumeration;

/**
 * Enumeration of possible resource TYPE values
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ResourceTypes extends Enumeration
{
    const A     = 1;
    const AAAA  = 28;
    const AFSDB = 18;
    const CNAME = 5;
    const HINFO = 13;
    const ISDN  = 20;
    const MB    = 7;
    const MD    = 3;
    const MF    = 4;
    const MG    = 8;
    const MINFO = 14;
    const MR    = 9;
    const MX    = 15;
    const NS    = 2;
    const NULL  = 10;
    const PTR   = 12;
    const RP    = 17;
    const RT    = 21;
    const SOA   = 6;
    const TXT   = 16;
    const WKS   = 11;
    const X25   = 19;
}
