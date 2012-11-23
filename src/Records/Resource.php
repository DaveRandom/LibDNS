<?php

  namespace DaveRandom\DNS\Records;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\Record;
  use \DaveRandom\DNS\DataType;
  use \DaveRandom\DNS\DataTypes\DomainName;

  class Resource extends Record {

    protected $ttl;
    protected $data;

    private static function parseResourceHeader(Packet $packet) {
      if (FALSE === $header = $packet->read(10)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return array_values(unpack('ntype/nclass/Nttl/nlength', $header));
    }

    public static function createFromPacket(Packet $packet) {
      $name = DomainName::createFromPacket($packet);
      list($type, $class, $ttl, $dataLength) = self::parseResourceHeader($packet);
      $data = DataType::createFromPacket($packet, $dataLength, $type);
      return new self($name, $type, $class, $ttl, $data);
    }

    public function __construct(DomainName $name, $type, $class, $ttl, DataType $data) {
      parent::__construct($name, $type, $class);
      $this->ttl = $ttl;
      $this->data = $data;
    }

    public function getRawData() {
      $result = $this->name->getRawData()
              . pack('nnNn', $this->type, $this->class, $this->ttl, strlen($this->data->getRawData()))
              . $this->data->getRawData();
      return $result;
    }

    public function getTTL() {
      return $this->ttl;
    }

    public function getData() {
      return $this->data;
    }

  }
