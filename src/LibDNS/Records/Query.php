<?php

  namespace DaveRandom\LibDNS\Records;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\Record;
  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class Query extends Record {

    public static function createFromPacket(Packet $packet) {
      $name = DomainName::createFromPacket($packet);
      if (FALSE === $meta = $packet->read(4)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      list($type, $class) = array_values(unpack('ntype/nclass', $meta));
      return new self($name, $type, $class);
    }

  }
