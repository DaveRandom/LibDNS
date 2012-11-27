<?php

  namespace DaveRandom\LibDNS;

  class Packet {

    private $length;
    private $data = '';
    private $pointer = 0;

    public function __construct($data = NULL) {
      if ($data instanceof Message) {
        $this->loadMessage($data);
      } else if ($data !== NULL) {
        $this->loadString((string) $data);
      }
    }
    public function __toString() {
      return $this->getData();
    }

    public function getData() {
      return $this->data;
    }
    public function getLength() {
      return $this->length;
    }

    public function loadMessage(Message $message) {
      $message->writeToPacket($this);
      return $this;
    }
    public function loadString($string) {
      $this->write($string);
      return $this;
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

    public function write($data) {
      $this->data .= $data;
      $this->length = strlen($this->data);
      return strlen($data);
    }

  }
