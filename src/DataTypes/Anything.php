<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class Anything extends DataType {

    protected $type = 8;

    private $data = '';

    public static function createFromPacket(Packet $packet, $dataLength, $type = NULL) {
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
