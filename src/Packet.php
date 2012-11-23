<?php

  namespace DaveRandom\DNS;

  class Packet {

    private $length;
    private $data;
    private $pointer = 0;

    public function __construct($data) {
      $this->length = strlen($data);
      $this->data = $data;
      if (!is_string($data) || !$this->length) {
        throw new \InvalidArgumentException('Invalid data');
      }
    }

    public function __get($name) {
      return $name === 'length' ? $this->length : NULL;
    }

    public function tell() {
      return $this->pointer;
    }

    public function seek($position) {
      $position = (int) $position;
      if ($position >= $this->length) {
        return FALSE;
      }
      if ($position < 0) {
        $position += $this->length;
      }
      $this->pointer = $position;
      return TRUE;
    }

    public function read($length) {
      if ($this->pointer + $length > $this->length) {
        return FALSE;
      }
      $chunk = substr($this->data, $this->pointer, $length);
      $this->pointer += $length;
      return $chunk;
    }

    public function readAbsolute($start, $length) {
      if ($start < 0) {
        $start += $this->length;
      }
      if ($length < 0) {
        $length += $this->length - $start;
      }
      if ($start >= $this->length || $start + $length > $this->length) {
        return FALSE;
      }
      return substr($this->data, $start, $length);
    }

  }
