<?php

  namespace DaveRandom\DNS;

  interface IEntity {

    const RECORDTYPE_A     = 1;
    const RECORDTYPE_NS    = 2;
    const RECORDTYPE_MD    = 3;
    const RECORDTYPE_MF    = 4;
    const RECORDTYPE_CNAME = 5;
    const RECORDTYPE_SOA   = 6;
    const RECORDTYPE_MB    = 7;
    const RECORDTYPE_MG    = 8;
    const RECORDTYPE_MR    = 9;
    const RECORDTYPE_NULL  = 10;
    const RECORDTYPE_WKS   = 11;
    const RECORDTYPE_PTR   = 12;
    const RECORDTYPE_HINFO = 13;
    const RECORDTYPE_MINFO = 14;
    const RECORDTYPE_MX    = 15;
    const RECORDTYPE_TXT   = 16;
    const RECORDTYPE_AXFR  = 252;
    const RECORDTYPE_MAILB = 253;
    const RECORDTYPE_MAILA = 254;
    const RECORDTYPE_ALL   = 255;

    const RECORDCLASS_IN  = 1;
    const RECORDCLASS_CS  = 2;
    const RECORDCLASS_CH  = 3;
    const RECORDCLASS_HS  = 4;
    const RECORDCLASS_ANY = 255;

    public function getRawData();

  }
