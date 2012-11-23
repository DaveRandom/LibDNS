<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class DomainName extends DataType {

    protected $type = 1;

    private $name;

    public static function createFromPacket(Packet $packet, $dataLength = NULL, $type = NULL) {
      $position = $packet->tell();
      $result = new self(self::recordNameToArray($packet, $position));
      $packet->seek($position);
      return $result;
    }

    private static function recordNameToArray(Packet $packet, &$position) {
      $name = array();
      $complete = FALSE;
      while ($position < $packet->length) {
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
          $name = array_merge($name, self::recordNameToArray($packet, $offset));
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

    public function __construct($name) {
      if (is_array($name)) {
        $this->name = $name;
      } else if (is_string($name)) {
        $this->name = explode('.', trim($name));
      } else {
        throw new \InvalidArgumentException('Invalid data type');
      }
    }

    public function getRawData() {
      $result = '';
      foreach ($this->name as $label) {
        $result .= chr(strlen($label)).$label;
      }
      $result .= "\x00";
      return $result;
    }

    public function getFormattedData() {
      return implode('.', $this->name);
    }

  }
