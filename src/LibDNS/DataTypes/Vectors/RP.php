<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class RP extends Vector {

    private $mailData;
    private $txtData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $mail = (new DomainName)->loadFromPacket($packet);
      $txt = (new DomainName)->loadFromPacket($packet);
      $this->__construct($mail, $txt);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock(TRUE)
        ->writeDomainName($this->mailData)
        ->writeDomainName($this->txtData);
    }

    public function __construct($mail = NULL, $txt = NULL) {
      $this->mailData = $mail instanceof DomainName ? $mail : new DomainName($mail);
      $this->txtData = $txt instanceof DomainName ? $txt : new DomainName($txt);
    }

    protected function constructRawData() {
      return $this->mailData->getRawData().$this->txtData->getRawData();
    }

    protected function constructFormattedData() {
      return 'Mail: '.$this->mailData->getFormattedData().' TXT: '.$this->txtData->getFormattedData();
    }

    public function mailAddress($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->mailData;
      } else {
        $this->mailData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

    public function txtLocation($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->txtData;
      } else {
        $this->txtData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

  }
