<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\DomainName;

  class AFSDB extends Vector {

    private $subtypeData;
    private $hostnameData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $subtype = (new Short)->loadFromPacket($packet);
      $hostname = (new DomainName)->loadFromPacket($packet);
      $this->__construct($subtype, $hostname);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock(TRUE)
        ->write($this->subtypeData->getRawData())
        ->writeDomainName($this->hostnameData);
    }

    public function __construct($subtype = NULL, $hostname = NULL) {
      $this->subtypeData = $subtype instanceof Short ? $subtype : new Short($subtype);
      $this->hostnameData = $hostname instanceof DomainName ? $hostname : new DomainName($hostname);
    }

    protected function constructRawData() {
      return $this->subtypeData->getRawData().$this->hostnameData->getRawData();
    }

    protected function constructFormattedData() {
      return 'Subtype: '.$this->subtypeData->getFormattedData().' Host: '.$this->hostnameData->getFormattedData();
    }

    public function subType($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->subtypeData;
      } else {
        $this->subtypeData = $newValue instanceof Short ? $newValue : new Short($newValue);
        $result = $this;
      }
      return $result;
    }

    public function hostName($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->hostnameData;
      } else {
        $this->hostnameData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

  }
