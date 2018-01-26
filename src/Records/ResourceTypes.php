<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Enum\Enum;

abstract class ResourceTypes extends Enum
{
    const A          = ResourceData\A::TYPE_ID; // RFC 1035
    const NS         = ResourceData\NS::TYPE_ID; // RFC 1035
    const MD         = ResourceData\MD::TYPE_ID; // RFC 1035, obsolete
    const MF         = ResourceData\MF::TYPE_ID; // RFC 1035, obsolete
    const CNAME      = ResourceData\CNAME::TYPE_ID; // RFC 1035
    const SOA        = ResourceData\SOA::TYPE_ID; // RFC 1035
    const MB         = ResourceData\MB::TYPE_ID; // RFC 1035, experimental
    const MG         = ResourceData\MG::TYPE_ID; // RFC 1035, experimental
    const MR         = ResourceData\MR::TYPE_ID; // RFC 1035, experimental
    const NULL       = ResourceData\NULLRecord::TYPE_ID; // RFC 1035, experimental;
    const WKS        = 11;
    const PTR        = ResourceData\PTR::TYPE_ID; // RFC 1035
    const HINFO      = 13;
    const MINFO      = 14;
    const MX         = 15;
    const TXT        = ResourceData\TXT::TYPE_ID; // RFC 1035
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
