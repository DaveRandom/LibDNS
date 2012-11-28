<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class Short extends DataType {

    private $short = 0;

    public function loadFromPacket(Packet $packet, $dataLength = 4) {
      if ($dataLength !== 2 || FALSE === $data = $packet->read(2)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct(current(unpack('n', $data)));
      return $this;
    }

    public function getRawData() {
      return pack('n', $this->short);
    }

    public function getFormattedData() {
      return $this->short;
    }

    public function setData($short) {
      if (is_scalar($short) || $short === NULL) {
        $short = (int) $short;
        if ($short < 0 || $short > 65535) {
          throw new \InvalidArgumentException('Value outside acceptable range for an unsigned short integer');
        }
        $this->short = $short;
      } else {
        throw new \InvalidArgumentException('Invalid data type');
      }
    }

  }
