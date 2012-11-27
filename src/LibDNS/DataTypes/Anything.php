<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class Anything extends DataType {

    private $data = '';

    public static function createFromPacket(Packet $packet, $dataLength) {
      if (FALSE === $data = $packet->read($dataLength)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return new self($data);
    }

    public function __construct($data) {
      if (!is_scalar($data) && $data !== NULL && !(is_object($data) && method_exists($data, '__toString'))) {
        throw new \InvalidArgumentException('Invalid data type');
      } else if (strlen($data) > 65535) {
        throw new \InvalidArgumentException('Maximum length of 65535 bytes exceded');
      }
      $this->data = (string) $data;
    }

    public function getRawData() {
      return $this->data;
    }

    public function getFormattedData() {
      return $this->data;
    }

  }
