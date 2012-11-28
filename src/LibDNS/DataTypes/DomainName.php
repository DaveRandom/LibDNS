<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\DataType;

  class DomainName extends DataType {

    private $tokens = array();

    private function recordNameToArray(Packet $packet, &$position) {
      $name = array();
      $complete = FALSE;
      while ($position < $packet->getLength()) {
        if (FALSE === $length = $packet->readAbsolute($position++, 1)) {
          throw new \InvalidArgumentException('Malformed packet');
        }
        if (!$length = ord($length)) {
          $complete = TRUE;
          break;
        }
        if (($length & 0xC0) === 0xC0) {
          if (FALSE === $pointer = $packet->readAbsolute($position - 1, 2)) {
            throw new \InvalidArgumentException('Malformed packet');
          }
          $offset = current(unpack('n', $pointer)) & 0x3FFF;
          $name = array_merge($name, $this->recordNameToArray($packet, $offset));
          $position++;
          $complete = TRUE;
          break;
        } else {
          if (FALSE === $name[] = $packet->readAbsolute($position, $length)) {
            throw new \InvalidArgumentException('Malformed packet');
          }
          $position += $length;
        }
      }
      if (!$complete) {
        throw new \UnexpectedValueException('Malformed domain name data at the specified offset');
      }
      return $name;
    }

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $position = $packet->tell();
      $data = $this->recordNameToArray($packet, $position);
      $packet->seek($position);
      $this->__construct($data);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock($withLengthWord)
        ->writeDomainName($this);
    }

    public function getRawData() {
      $result = '';
      foreach ($this->tokens as $label) {
        $result .= pack('C', strlen($label)).$label;
      }
      $result .= "\x00";
      return $result;
    }

    public function getFormattedData() {
      return implode('.', $this->tokens);
    }

    public function setData($name) {
      if (is_array($name)) {
        $this->tokens = $name;
      } else if (is_string($name)) {
        $this->tokens = explode('.', trim($name));
      } else {
        throw new \InvalidArgumentException('Invalid data type');
      }
    }

    public function getTokens() {
      return $this->tokens;
    }

  }
