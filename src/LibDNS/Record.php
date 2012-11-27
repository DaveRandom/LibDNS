<?php

  namespace DaveRandom\LibDNS;

  use \DaveRandom\LibDNS\DataTypes\DomainName;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;

  abstract class Record {

    const TYPE_A     = 1;
    const TYPE_NS    = 2;
    const TYPE_MD    = 3;
    const TYPE_MF    = 4;
    const TYPE_CNAME = 5;
    const TYPE_SOA   = 6;
    const TYPE_MB    = 7;
    const TYPE_MG    = 8;
    const TYPE_MR    = 9;
    const TYPE_NULL  = 10;
    const TYPE_WKS   = 11;
    const TYPE_PTR   = 12;
    const TYPE_HINFO = 13;
    const TYPE_MINFO = 14;
    const TYPE_MX    = 15;
    const TYPE_TXT   = 16;
    const TYPE_AAAA  = 28;
    const TYPE_AXFR  = 252;
    const TYPE_MAILB = 253;
    const TYPE_MAILA = 254;
    const TYPE_ALL   = 255;

    const CLASS_IN  = 1;
    const CLASS_CS  = 2;
    const CLASS_CH  = 3;
    const CLASS_HS  = 4;
    const CLASS_ANY = 255;

    protected $name;
    protected $type;
    protected $class;

    public function __construct($name, $type = self::TYPE_A, $class = self::CLASS_IN) {
      if ($name instanceof DomainName) {
        $this->name = $name;
      } else {
        $this->name = new DomainName($name);
      }
      $this->type = $type;
      $this->class = $class;
    }

    public function writeToPacket(PacketBuilder $packetBuilder) {
      $packetBuilder
        ->addWriteBlock()
        ->writeDomainName($this->name)
        ->write(pack('nn', $this->type, $this->class));
    }

    public function getName() {
      return $this->name;
    }

    public function getType() {
      return $this->type;
    }

    public function getClass() {
      return $this->class;
    }

  }
