<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\DomainName;
  use \DaveRandom\LibDNS\DataTypes\Short;

  class RT extends Vector {

    private $preferenceData;
    private $intermediateHostData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $preference = (new Short)->loadFromPacket($packet);
      $intermediateHost = (new DomainName)->loadFromPacket($packet);
      $this->__construct($preference, $intermediateHost);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock(TRUE)
        ->write($this->preferenceData->getRawData())
        ->writeDomainName($this->intermediateHostData);
    }

    public function __construct($preference = NULL, $intermediateHost = NULL) {
      $this->preferenceData = $preference instanceof Short ? $preference : new Short($preference);
      $this->intermediateHostData = $intermediateHost instanceof DomainName ? $intermediateHost : new DomainName($intermediateHost);
    }

    protected function constructRawData() {
      return $this->preferenceData->getRawData().$this->intermediateHostData->getRawData();
    }

    protected function constructFormattedData() {
      return $this->preferenceData->getFormattedData().' '.$this->intermediateHostData->getFormattedData();
    }

    public function preference($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->preferenceData;
      } else {
        $this->preferenceData = $newValue instanceof Short ? $newValue : new Short($newValue);
        $result = $this;
      }
      return $result;
    }

    public function intermediateHost($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->intermediateHostData;
      } else {
        $this->intermediateHostData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

  }
