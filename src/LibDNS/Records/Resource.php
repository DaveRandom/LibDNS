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

    private $dataTypes = array(
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

    private function parseResourceHeader(Packet $packet) {
      if (FALSE === $header = $packet->read(10)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return array_values(unpack('ntype/nclass/Nttl/nlength', $header));
    }
    private function createDataObject($packet, $dataLength, $type) {
      $class = '\\DaveRandom\\LibDNS\\DataTypes\\'.$this->dataTypes[$type];
      $obj = new $class;
      $obj->loadFromPacket($packet, $dataLength);
      return $obj;
    }

    public function loadFromPacket(Packet $packet) {
      $name = new DomainName;
      $name->loadFromPacket($packet);
      list($type, $class, $ttl, $dataLength) = $this->parseResourceHeader($packet);
      $data = $this->createDataObject($packet, $dataLength, $type);
      $this->__construct($name, $type, $class, $ttl, $data);
      return $this;
    }

    public function __construct($name = NULL, $type = self::TYPE_A, $class = self::CLASS_IN, $ttl = NULL, DataType $data = NULL) {
      parent::__construct($name, $type, $class);
      if ($ttl !== NULL) {
        $this->setTTL($ttl);
      }
      if ($data !== NULL) {
        $this->setData($data);
      }
    }

    public function writeToPacket(PacketBuilder $packetBuilder) {
      if ($this->ttl === NULL || $this->data === NULL) {
        throw new \Exception('Data incomplete');
      }
      try {
        $headBlock = $packetBuilder
          ->addWriteBlock()
          ->writeDomainName($this->name)
          ->write(pack('nnN', $this->type, $this->class, $this->ttl));
        $this->data->writeToPacket($packetBuilder, TRUE);
      } catch (\Exception $e) {
        $packetBuilder->removeWriteBlock($headBlock);
        throw $e;
      }
    }

    public function getTTL() {
      return $this->ttl;
    }
    public function setTTL($ttl) {
      $this->ttl = (int) $ttl;
    }

    public function getData() {
      return $this->data;
    }
    public function setData(DataType $data) {
      $this->data = $data;
    }

  }
