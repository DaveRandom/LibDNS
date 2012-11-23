<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class Char extends DataType {

    protected $type = 3;

    private $char = 0;

    public static function createFromPacket(Packet $packet, $dataLength = 1, $type = NULL) {
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

    public function getRawData() {
      return chr($this->char);
    }

    public function getFormattedData() {
      return $this->char;
    }

  }
