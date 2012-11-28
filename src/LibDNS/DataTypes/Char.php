<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class Char extends DataType {

    private $char = 0;

    public function loadFromPacket(Packet $packet, $dataLength = 1) {
      if ($dataLength !== 1 || FALSE === $data = $packet->read(1)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct(ord($data));
      return $this;
    }

    public function writeToPacket(Packet $packet, $withLengthWord = FALSE) {
      $packet->prepareWriteBlock($withLengthWord)->write(chr($this->char));
    }

    public function getFormattedData() {
      return $this->char;
    }

    public function setData() {
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

  }
