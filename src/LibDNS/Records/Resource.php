<?php

  namespace DaveRandom\LibDNS\Records;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\Record;
  use \DaveRandom\LibDNS\DataType;
  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class Resource extends Record {

    protected $ttl;
    protected $data;

    private static $dataTypes = array(
      1 => 'IPv4Address',      // A
      2 => 'DomainName',       // NS
      3 => 'DomainName',       // MD
      4 => 'DomainName',       // MF
      5 => 'DomainName',       // CNAME
      6 => 'Vectors\\SOA',     // SOA
      7 => 'DomainName',       // MB
      8 => 'DomainName',       // MG
      9 => 'DomainName',       // MR
      10 => 'Anything',        // NULL
      11 => 'Vectors\\WKS',    // WKS
      12 => 'DomainName',      // PTR
      13 => 'Vectors\\HINFO',  // HINFO
      14 => 'Vectors\\MINFO',  // MINFO
      15 => 'Vectors\\MX',     // MX
      16 => 'CharacterString', // TXT
      28 => 'IPv6Address'      // AAAA
    );

    private static function parseResourceHeader(Packet $packet) {
      if (FALSE === $header = $packet->read(10)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return array_values(unpack('ntype/nclass/Nttl/nlength', $header));
    }
    private static function createDataObject($packet, $dataLength, $type) {
      $class = '\\DaveRandom\\LibDNS\\DataTypes\\'.self::$dataTypes[$type];
      return $class::createFromPacket($packet, $dataLength);
    }

    public static function createFromPacket(Packet $packet) {
      $name = DomainName::createFromPacket($packet);
      list($type, $class, $ttl, $dataLength) = self::parseResourceHeader($packet);
      $data = self::createDataObject($packet, $dataLength, $type);
      return new self($name, $type, $class, $ttl, $data);
    }

    public function __construct(DomainName $name, $type, $class, $ttl, DataType $data) {
      parent::__construct($name, $type, $class);
      $this->ttl = $ttl;
      $this->data = $data;
    }

    public function writeToPacket(PacketBuilder $packetBuilder) {
      parent::writeToPacket($packetBuilder);
      $ttlBlock = $packetBuilder
        ->addWriteBlock()
        ->write(pack('N', $this->ttl));
      try {
        $this->data->writeToPacket($packetBuilder, TRUE);
      } catch (\Exception $e) {
        $packetBuilder->removeWriteBlock($ttlBlock);
        throw $e;
      }
    }

    public function getTTL() {
      return $this->ttl;
    }

    public function getData() {
      return $this->data;
    }

  }
