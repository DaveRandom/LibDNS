<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Enum\Enum;

abstract class ResourceTypes extends Enum
{
    // RFC 1035
    const A = 1;
    const NS = 2;
    const CNAME = 5;
    const SOA = 6;
    const PTR = 12;
    const MX = 15;
    const TXT = 16;

    // RFC 1183
    const RP = 17;

    // RFC 3596
    const AAAA = 28;

    // RFC 1876
    const LOC = 29; // todo

    // RFC 2782
    const SRV = 33; // todo

    // RFC 3403
    const NAPTR = 35; // todo

    // RFC 2230
    const KX = 36; // todo

    // RFC 4398
    const CERT = 37; // todo

    // RFC 6672
    const DNAME = 39;

    // RFC 6891
    const OPT = 41; // todo

    // RFC 3123
    const APL = 42; // todo

    // RFC 4134
    const DS = 43; // todo

    // RFC 4255
    const SSHFP = 44; // todo

    // RFC 4025
    const IPSECKEY = 45; // todo

    // RFC 4034
    const RRSIG = 46; // todo
    const NSEC = 47; // todo
    const DNSKEY = 48; // todo

    // RFC 4701
    const DHCID = 49; // todo

    // RFC 5155
    const NSEC3 = 50; // todo
    const NSEC3PARAM = 51; // todo

    // RFC 6698
    const TLSA = 52; // todo

    // RFC 8162
    const SMIMEA = 53; // todo

    // RFC 8005
    const HIP = 55; // todo

    // RFC 7344
    const CDS = 59; // todo
    const CDNSKEY = 60; // todo

    // RFC 7929
    const OPENPGPKEY = 61; // todo

    // RFC 7477
    const CSYNC = 62; // todo

    // RFC 6742
    const NID = 104; // todo
    const L32 = 105; // todo
    const L64 = 106; // todo
    const LP = 107; // todo

    // RFC 7043
    const EUI48 = 108; // todo
    const EUI64 = 109; // todo

    // RFC 2930
    const TKEY = 249; // todo

    // RFC 2845
    const TSIG = 250; // todo

    // RFC 7553
    const URI = 256; // todo

    // RFC 6844
    const CAA = 257; // todo

    // DNSSEC
    const TA = 32768; // todo

    // RFC 4431
    const DLV = 32769; // todo

    /** @deprecated */ const MD = 3;
    /** @deprecated */ const MF = 4;
    /** @deprecated */ const MB = 7;
    /** @deprecated */ const MG = 8;
    /** @deprecated */ const MR = 9;
    /** @deprecated */ const NULL = 10;
    /** @deprecated */ const WKS = 11;
    /** @deprecated */ const HINFO = 13;
    /** @deprecated */ const MINFO = 14;
    /** @deprecated */ const AFSDB = 18;
    /** @deprecated */ const X25 = 19;
    /** @deprecated */ const ISDN = 20;
    /** @deprecated */ const RT = 21;
    /** @deprecated */ const NSAP = 22;
    /** @deprecated */ const NSAP_PTR = 23;
    /** @deprecated */ const SIG = 24;
    /** @deprecated */ const KEY = 25;
    /** @deprecated */ const PX = 26;
    /** @deprecated */ const GPOS = 27;
    /** @deprecated */ const NXT = 30;
    /** @deprecated */ const EID = 31;
    /** @deprecated */ const NIMLOC = 32;
    /** @deprecated */ const NB = 32;
    /** @deprecated */ const NBSTAT = 33;
    /** @deprecated */ const ATMA = 34;
    /** @deprecated */ const A6 = 38;
    /** @deprecated */ const SINK = 40;
    /** @deprecated */ const NINFO = 56;
    /** @deprecated */ const RKEY = 57;
    /** @deprecated */ const TALINK = 58;
    /** @deprecated */ const SPF = 99;
    /** @deprecated */ const UINFO = 100;
    /** @deprecated */ const UID = 101;
    /** @deprecated */ const GID = 102;
    /** @deprecated */ const UNSPEC = 103;
    /** @deprecated */ const AVC = 258;
    /** @deprecated */ const DOA = 259;
}
