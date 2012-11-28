<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class IPv4Address extends DataType {

    private $octets = array();

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

    public function loadFromPacket(Packet $packet, $dataLength = 4) {
      if ($dataLength !== 4 || FALSE === $data = $packet->read(4)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct(array_values(unpack('C*', $data)));
      return $this;
    }

    public function getRawData() {
      return call_user_func_array('pack', array_merge(array('C*'), $this->octets));
    }

    public function getFormattedData() {
      return implode('.', $this->octets);
    }

    public function setData($address) {
      if (is_int($address)) {
        $octets = $this->longToOctets($address);
      } else if (is_array($address)) {
        $octets = $address;
      } else if (is_string($address)) {
        if (!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
          throw new \InvalidArgumentException('Invalid address data');
        }
        $octets = explode('.', $address);
      } else {
        throw new \InvalidArgumentException('Invalid address data');
      }
      $octets = array_filter(array_map('intval', $octets), array($this, 'validateOctetValue'));
      if (count($octets) !== 4) {
        throw new \InvalidArgumentException('Invalid address data');
      }
      $this->octets = $octets;
    }

    public function getOctets() {
      return $this->octets;
    }

  }
