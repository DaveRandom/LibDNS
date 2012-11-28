<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class MINFO extends Vector {

    private $rMailData;
    private $eMailData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $rMail = (new DomainName)->loadFromPacket($packet);
      $eMail = (new DomainName)->loadFromPacket($packet);
      $this->__construct($rMail, $eMail);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock(TRUE)
        ->writeDomainName($this->rMailData)
        ->writeDomainName($this->eMailData);
    }

    public function __construct($rMail = NULL, $eMail = NULL) {
      $this->rMailData = $rMail instanceof DomainName ? $rMail : new DomainName($rMail);
      $this->eMailData = $eMail instanceof DomainName ? $eMail : new DomainName($eMail);
    }

    protected function constructRawData() {
      return $this->rMailData->getRawData().$this->eMailData->getRawData();
    }

    protected function constructFormattedData() {
      return 'Responsible: '.$this->rMailData->getFormattedData().' Error: '.$this->eMailData->getFormattedData();
    }

    public function responsibleMailAddress($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->rMailData;
      } else {
        $this->rMailData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

    public function errorMailAddress($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->eMailData;
      } else {
        $this->eMailData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

  }
