<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\BitMap;
  use \DaveRandom\LibDNS\DataTypes\Char;
  use \DaveRandom\LibDNS\DataTypes\IPv4Address;

  class WKS extends Vector {

    private $addressData;
    private $protocolData;
    private $servicesData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $address = (new IPv4Address)->loadFromPacket($packet, 4);
      $protocol = (new Char)->loadFromPacket($packet, 1);
      $services = (new BitMap)->loadFromPacket($packet, $dataLength - 5);
      $this->__construct($address, $protocol, $services);
      return $this;
    }

    public function __construct($address = NULL, $protocol = NULL, $services = NULL) {
      $this->addressData = $address instanceof IPv4Address ? $address : new IPv4Address($address);
      $this->protocolData = $protocol instanceof Char ? $protocol : new Char($protocol);
      $this->servicesData = $services instanceof BitMap ? $services : new BitMap($services);
    }

    protected function constructRawData() {
      return $this->addressData->getRawData().$this->protocolData->getRawData().$this->servicesData->getRawData();
    }

    protected function constructFormattedData() {
      return $this->addressData->getFormattedData().' ('.$this->protocolData->getFormattedData().'): '.$this->servicesData->getFormattedData();
    }

    public function address($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->addressData;
      } else {
        $this->addressData = $newValue instanceof IPv4Address ? $newValue : new IPv4Address($newValue);
        $result = $this;
      }
      return $result;
    }

    public function protocol($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->protocolData;
      } else {
        $this->protocolData = $newValue instanceof Char ? $newValue : new Char($newValue);
        $result = $this;
      }
      return $result;
    }

    public function services($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->servicesData;
      } else {
        $this->servicesData = $newValue instanceof BitMap ? $newValue : new BitMap($newValue);
        $result = $this;
      }
      return $result;
    }

  }
