<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class Long extends DataType {

    // All longs in DNS are unsigned, so we have to compensate for PHP's lack of an unsigned
    // integer type on 32-bit platforms (because pack() doesn't play nice with unsigned longs
    // with respect to explicit byte order)

    private $octets = array();

    private function validateLongRange($long) {
      if (PHP_INT_MAX < pow(2, 32)) {
        return TRUE; // 32-bit host, always return TRUE because we validated the data as an int
      } else {
        return $long >= 0 && $long <= 0xFFFFFFFF;
      }
    }
    private function longToOctets($long) {
      return array(
        ($long >> 24) & 0xFF,
        ($long >> 16) & 0xFF,
        ($long >> 8) & 0xFF,
        $long & 0xFF
      );
    }
    private function octetsToLong($octets) {
      $long = 0;
      $long |= ($octets[0] << 24);
      $long |= ($octets[1] << 16);
      $long |= ($octets[2] << 8);
      $long |= $octets[3];
      return $long;
    }

    public function loadFromPacket(Packet $packet, $dataLength = 4) {
      if ($dataLength !== 4 || FALSE === $data = $packet->read(4)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct(current(unpack('N', $data)));
      return $this;
    }

    public function getRawData() {
      return call_user_func_array('pack', array_merge(array('C*'), $this->octets));
    }

    public function getFormattedData() {
      return $this->octetsToLong($this->octets);
    }

    public function setData($long) {
      if (!is_int($long)) {
        if (is_scalar($long) || $long === NULL) {
          // 32bit-safe way of casting to int
          $long = current(unpack('N', pack('N', (float) $long)));
        } else {
          throw new \InvalidArgumentException('Invalid data type');
        }
      }
      if (!$this->validateLongRange($long)) {
        throw new \InvalidArgumentException('Value outside acceptable range for an unsigned long integer');
      }
      $this->octets = $this->longToOctets($long);
    }

  }
