<?php declare(strict_types=1);
/**
 * Enumeration of possible resource TYPE values
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
 * Enumeration of possible resource TYPE values
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
abstract class ResourceTypes extends Enum
{
    const A          = 1;
    const NS         = 2;
    const MD         = 3;
    const MF         = 4;
    const CNAME      = 5;
    const SOA        = 6;
    const MB         = 7;
    const MG         = 8;
    const MR         = 9;
    const NULL       = 10;
    const WKS        = 11;
    const PTR        = 12;
    const HINFO      = 13;
    const MINFO      = 14;
    const MX         = 15;
    const TXT        = 16;
    const RP         = 17;
    const AFSDB      = 18;
    const X25        = 19;
    const ISDN       = 20;
    const RT         = 21;
    const SIG        = 24;
    const KEY        = 25;
    const AAAA       = 28;
    const LOC        = 29;
    const SRV        = 33;
    const NAPTR      = 35;
    const KX         = 36;
    const CERT       = 37;
    const DNAME      = 39;
//    const APL        = 42;
    const DS         = 43;
//    const IPSECKEY   = 45;
//    const RRSIG      = 46;
//    const NSEC       = 47;
    const DNSKEY     = 48;
    const DHCID      = 49;
//    const NSEC3      = 50;
//    const NSEC3PARAM = 50;
//    const HIP        = 55;
    const SPF        = 99;
    const CAA        = 257;
    const DLV        = 32769;
}
