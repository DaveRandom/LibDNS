<?php

  namespace DaveRandom\LibDNS\PacketBuilder;

  use DaveRandom\LibDNS\Packet;

  class PacketBuilder {

    private $length = 0;

    private $domainNamePointers = array();
    private $writeBlocks = array();

    private $enableCompression;

    public function __construct($enableCompression) {
      $this->enableCompression = $enableCompression;
    }

    public function getLength() {
      return $this->length;
    }
    public function getData() {
      $data = '';
      while ($block = array_shift($this->writeBlocks)) {
        $data .= $block->getData();
      }
      return $data;
    }

    public function addWriteBlock($hasLengthWord = FALSE) {
      return $this->writeBlocks[] = new WriteBlock($this, (bool) $hasLengthWord);
    }
    public function removeWriteBlock(WriteBlock $block) {
      for ($i = count($this->writeBlocks) - 1; $i >= 0; $i--) {
        if ($block === $this->writeBlocks[$i]) {
          array_splice($this->writeBlocks, $i, 1);
          break;
        }
      }
    }

    public function updateLength($length) {
      $this->length += $length;
    }

    public function write(Packet $packet = NULL) {
      if ($packet === NULL) {
        $packet = new Packet;
      }
      $packet->write($this->getData());
      return $packet;
    }

    public function storeDomainName($tokens) {
      $literals = array();
      $pointer = "\x00";
      $data = '';
      while ($tokens) {
        if ($this->enableCompression) {
          $name = implode('.', $tokens);
          if (isset($this->domainNamePointers[$name])) {
            $pointer = $this->domainNamePointers[$name];
            break;
          }
        }
        $literals[] = array_shift($tokens);
      }
      $position = $this->length;
      while ($literals) {
        $this->domainNamePointers[implode('.', array_merge($literals, $tokens))] = pack('n', $position | 0xC000);
        $token = array_shift($literals);
        $tokenLength = strlen($token);
        $data .= chr($tokenLength) . $token;
        $position += $tokenLength + 1;
      }
      return $data . $pointer;
    }

  }
