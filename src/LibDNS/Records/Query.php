<?php

  namespace DaveRandom\LibDNS\Records;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\Record;
  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class Query extends Record {

    public function loadFromPacket(Packet $packet) {
      $name = new DomainName;
      $name->loadFromPacket($packet);
      if (FALSE === $meta = $packet->read(4)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      list($type, $class) = array_values(unpack('ntype/nclass', $meta));
      $this->__construct($name, $type, $class);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder) {
      $packetBuilder
        ->addWriteBlock()
        ->writeDomainName($this->name)
        ->write(pack('nn', $this->type, $this->class));
    }

  }
