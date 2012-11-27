<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class Short extends DataType {

    private $short = 0;

    public static function createFromPacket(Packet $packet, $dataLength = 2) {
      if (FALSE === $data = $packet->read(2)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return new self(current(unpack('n', $data)));
    }

    public function __construct($short) {
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

    public function getRawData() {
      return pack('n', $this->short);
    }

    public function getFormattedData() {
      return $this->short;
    }

  }
