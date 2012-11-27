<?php

  namespace DaveRandom\LibDNS\PacketBuilder;

  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class WriteBlock {

    private $data = '';
    private $length = 0;

    private $packetBuilder;
    private $hasLengthWord;

    private function processWriteLength($length) {
      $this->packetBuilder->updateLength($length);
      if ($this->packetBuilder->getLength() > 512) {
        $this->packetBuilder->removeWriteBlock($this);
        throw new \Exception('Maximum packet length exceded');
      }
    }

    public function __construct(PacketBuilder $packetBuilder, $hasLengthWord) {
      $this->packetBuilder = $packetBuilder;
      $this->hasLengthWord = $hasLengthWord;
      if ($hasLengthWord) {
        $this->packetBuilder->updateLength(2);
      }
    }

    public function getLength() {
      return $this->length;
    }
    public function getData() {
      $data = '';
      if ($this->hasLengthWord) {
        $data .= pack('n', strlen($this->data));
      }
      $data .= $this->data;
      return $data;
    }

    public function write($data) {
      $this->data .= $data;
      $this->processWriteLength(strlen($data));
      return $this;
    }
    public function overwrite($data) {
      $lengthDiff = strlen($data) - strlen($this->data);
      $this->data = $data;
      if ($lengthDiff) {
        $this->processWriteLength($lengthDiff);
      }
      return $this;
    }

    public function writeDomainName(DomainName $name) {
      return $this->write($this->packetBuilder->storeDomainName($name->getTokens()));
    }

  }
