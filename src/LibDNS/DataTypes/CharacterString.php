<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class CharacterString extends DataType {

    private $string = '';
    private $length = 0;

    public function loadFromPacket(Packet $packet, $dataLength = null) {
      if ((false === $length = ord($packet->read(1))) || (false === $data = $packet->read($length))) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct($data);
      return $this;
    }

    public function getRawData() {
      return chr($this->length).$this->string;
    }

    public function getFormattedData() {
      return $this->string;
    }

    public function setData($string = NULL) {
      if (!is_scalar($string) && $string !== NULL && !(is_object($string) && method_exists($string, '__toString'))) {
        throw new \InvalidArgumentException('Invalid data type');
      } else if (($length = strlen($string)) > 255) {
        throw new \InvalidArgumentException('Maximum length of 255 characters exceded');
      }
      $this->length = $length;
      $this->string = (string) $string;
    }

    public function getLength() {
      return $this->length;
    }

  }
