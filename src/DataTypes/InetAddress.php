<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class InetAddress extends DataType {

    protected $type = 2;

    private $octets = array();

    public static function createFromPacket(Packet $packet, $dataLength = 4, $type = NULL) {
      if (FALSE === $data = $packet->read(4)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return new self(array_values(unpack('C*', $data)));
    }

    private function longToOctets($long) {
      return array(
        ($long >> 24) & 0xFF,
        ($long >> 16) & 0xFF,
        ($long >> 8) & 0xFF,
        $long & 0xFF
      );
    }

    private function validateOctetValue($octet) {
      return $octet >= 0 && $octet <= 255;
    }

    public function __construct($address) {
      if (is_int($address)) {
        $this->octets = $this->longToOctets($address);
      } else if (is_array($address)) {
        $this->octets = $address;
      } else if (is_string($address)) {
        $this->octets = explode('.', $address);
        if (count($this->octets) !== 4) {
          throw new \InvalidArgumentException('Invalid address data');
        }
      }
      $this->octets = array_filter(array_map('intval', $this->octets), array($this, 'validateOctetValue'));
      if (count($this->octets) !== 4) {
        throw new \InvalidArgumentException('Invalid address data');
      }
    }

    public function getRawData() {
      return pack('C*', $this->octets[0], $this->octets[1], $this->octets[2], $this->octets[3]);
    }

    public function getFormattedData() {
      return implode('.', $this->octets);
    }

  }
