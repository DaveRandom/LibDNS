<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class BitMap extends DataType {

    protected $type = 7;

    private $map = array();

    public static function createFromPacket(Packet $packet, $dataLength, $type = NULL) {
      if (FALSE === $data = $packet->read($dataLength)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      return new self($data);
    }

    private function parseStringToMap($data) {
      $map = array();
      if (is_array($data)) {
        foreach ($data as $bit) {
          $map[] = (int)(bool)$bit;
        }
      } else if (is_string($data)) {
        foreach (unpack('C*', $data) as $char) {
          for ($i = 7; $i >=0; $i--) {
            $map[] = ($char >> $i) & 0x01;
          }
        }
      } else if (is_int($data)) {
        for ($i = round(log(PHP_INT_MAX, 2)); $i >= 0; $i--) {
          $map[] = ($data >> $i) & 0x01;
        }
      } else {
        throw new \InvalidArgumentException('Invalid data type');
      }
      return $map;
    }
    private function createStringFromMap($map) {
      $chars = array('C*');
      end($map);
      $last = key($map);
      for ($i = $j = $char = 0; $i <= $last; $i++) {
        $bit = isset($map[$i]) ? $map[$i] : 0;
        $char |= ($bit << (7 - $j));
        if (++$j === 8) {
          $chars[] = $char;
          $j = $char = 0;
        }
      }
      if ($char) {
        $chars[] = $char;
      }
      return call_user_func_array('pack', $chars);
    }

    public function __construct($data) {
      $this->map = $this->parseStringToMap($data);
    }

    public function getRawData() {
      return $this->createStringFromMap($this->map);
    }

    public function getFormattedData() {
      $data = $this->createStringFromMap($this->map);
      return call_user_func_array(
        'sprintf',
        array_merge(
          array(str_repeat('%08b', strlen($data))),
          array_values(unpack('C*', $data))
        )
      );
    }

    public function getMap() {
      return $this->map;
    }

    public function valueAtPosition($position, $newValue = NULL) {
      if ($newValue = NULL) {
        $result = isset($this->map) ? $this->map[$position] : 0;
      } else {
        $this->map[$position] = (int)(bool) $newValue;
        $result = $this;
      }
      return $result;
    }

  }
