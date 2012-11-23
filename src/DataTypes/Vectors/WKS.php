<?php

  namespace DaveRandom\DNS\DataTypes\Vectors;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataTypes\Vector;
  use \DaveRandom\DNS\DataTypes\InetAddress;
  use \DaveRandom\DNS\DataTypes\Char;
  use \DaveRandom\DNS\DataTypes\BitMap;

  class WKS extends Vector {

    private $addressData;
    private $protocolData;
    private $servicesData;

    public static function createFromPacket(Packet $packet, $dataLength, $type = NULL) {
      $address = InetAddress::createFromPacket($packet);
      $protocol = Char::createFromPacket($packet);
      $services = BitMap::createFromPacket($packet, $dataLength - 5);
      return new self($address, $protocol, $services);
    }

    public function __construct($address, $protocol, $services) {
      $this->addressData = $address instanceof InetAddress ? $address : new InetAddress($address);
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
        $this->addressData = $newValue instanceof InetAddress ? $newValue : new InetAddress($newValue);
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
