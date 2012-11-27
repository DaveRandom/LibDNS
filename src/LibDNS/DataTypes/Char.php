<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class Char extends DataType {

    private $char = 0;

    public static function createFromPacket(Packet $packet, $dataLength = 1) {
      if (FALSE === $data = $packet->read(1)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return new self(ord($data));
    }

    public function __construct($char) {
      if (is_scalar($char) || $char === NULL) {
        $char = (int) $char;
        if ($char < 0 || $char > 255) {
          throw new \InvalidArgumentException('Value outside acceptable range for an unsigned char');
        }
        $this->char = $char;
      } else {
        throw new \InvalidArgumentException('Invalid data type');
      }
    }

    public function writeToPacket(Packet $packet, $withLengthWord = FALSE) {
      $packet->prepareWriteBlock($withLengthWord)->write(chr($this->char));
    }

    public function getFormattedData() {
      return $this->char;
    }

  }
