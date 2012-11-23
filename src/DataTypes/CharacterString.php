<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class CharacterString extends DataType {

    protected $type = 6;

    private $string;
    private $length;

    public static function createFromPacket(Packet $packet, $dataLength = NULL, $type = NULL) {
      if ((FALSE === $length = ord($packet->read(1))) || (FALSE === $data = $packet->read($length))) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return new self($data);
    }

    public function __construct($string) {
      if (!is_scalar($string) && $string !== NULL && !(is_object($string) && method_exists($string, '__toString'))) {
        throw new \InvalidArgumentException('Invalid data type');
      } else if (($length = strlen($string)) > 255) {
        throw new \InvalidArgumentException('Maximum length of 255 characters exceded');
      }
      $this->length = $length;
      $this->string = (string) $string;
    }

    public function getRawData() {
      return chr(strlen($this->length)).$this->string;
    }

    public function getFormattedData() {
      return $this->string;
    }

  }
