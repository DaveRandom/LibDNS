<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  class IPv6Address extends DataType {

    private $blocks = array();

    private function addressStringToBlockArray($address) {
      $address = preg_replace('/\s*/', '', $address);
      $blocks = explode(':', $address);
      if (FALSE !== $pos = strpos($address, '::')) {
        $spliceCount = 9 - count($blocks);
        if ($pos === 0) {
          $splicePos = 0;
        } else if ($pos === strlen($address) - 2) {
          $splicePos = 8 - $spliceCount;
        } else {
          $splicePos = array_search('', $blocks, TRUE)) {
        }
        array_splice($blocks, $splicePos, 1, array_fill(0, $spliceCount, '0'));
      }
      return array_map('hexdec', $blocks);
    }
    private function blockArrayToAddressString($blocks) {
      $zeroPos = -1;
      $zeroCounter = $zeroMax = 0;
      foreach ($blocks as $i => &$block) {
        if (((int) $block) === 0) {
          $zeroCounter++;
        } else if ($zeroCounter) {
          if ($zeroCounter > 1 && $zeroCounter > $zeroMax) {
            $zeroMax = $zeroCounter;
            $zeroPos = $i - $zeroCounter;
          }
          $zeroCounter = 0;
        }
        $block = dechex($block);
      }
      if ($zeroCounter > 1 && $zeroCounter > $zeroMax) {
        $zeroMax = $zeroCounter;
        $zeroPos = $i - $zeroCounter + 1;
      }
      if ($zeroPos > -1) {
        $replace = array('');
        if ($zeroPos === 0 || $zeroPos + $zeroMax === 8) {
          $replace[] = '';
        }
        array_splice($blocks, $zeroPos, $zeroMax, $replace);
      }
      return implode(':', $blocks);
    }

    private function validateBlockArray($blocks) {
      $result = array_filter(array_map('intval', $blocks), array($this, 'validateBlockValue'));
      return count($result) === 8 ? $result : FALSE;
    }
    private function validateBlockValue($block) {
      return $block >= 0 && $block <= 65535;
    }

    public function loadFromPacket(Packet $packet, $dataLength = 16) {
      if ($dataLength !== 16 || FALSE === $data = $packet->read(16)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct(array_values(unpack('n*', $data)));
      return $this;
    }

    public function getRawData() {
      return call_user_func_array('pack', array_merge(array('n*'), $this->blocks));
    }

    public function getFormattedData() {
      return $this->blockArrayToAddressString($this->blocks);
    }

    public function setData($address) {
      if (is_string($address)) {
        if (!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
          throw new \InvalidArgumentException('Invalid address data');
        }
        $address = $this->addressStringToBlockArray($address);
      }
      if (!is_array($address) || !$blocks = $this->validateBlockArray($address)) {
        throw new \InvalidArgumentException('Invalid address data');
      }
      $this->blocks = $blocks;
    }

    public function getBlocks() {
      return $this->blocks;
    }

  }
