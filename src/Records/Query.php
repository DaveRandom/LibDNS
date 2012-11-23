<?php

  namespace DaveRandom\DNS\Records;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\Record;
  use \DaveRandom\DNS\DataTypes\DomainName;

  class Query extends Record {

    public static function createFromPacket(Packet $packet) {
      $name = DomainName::createFromPacket($packet);
      if (FALSE === $meta = $packet->read(4)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      list($type, $class) = array_values(unpack('ntype/nclass', $meta));
      return new self($name, $type, $class);
    }

    public function getRawData() {
      $result = $this->name->getRawData() . pack('nn', $this->type, $this->class);
      return $result;
    }

  }
