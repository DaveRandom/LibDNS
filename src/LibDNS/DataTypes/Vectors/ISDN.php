<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\CharacterString;

  class ISDN extends Vector {

    private $isdnAddressData;
    private $subAddressData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $isdnAddress = (new CharacterString)->loadFromPacket($packet);
      $subAddress = (new CharacterString)->loadFromPacket($packet);
      $this->__construct($isdnAddress, $subAddress);
      return $this;
    }

    public function __construct($isdnAddress = NULL, $subAddress = NULL) {
      $this->isdnAddressData = $isdnAddress instanceof CharacterString ? $isdnAddress : new CharacterString($isdnAddress);
      $this->subAddressData = $subAddress instanceof CharacterString ? $subAddress : new CharacterString($subAddress);
    }

    protected function constructRawData() {
      return $this->isdnAddressData->getRawData().$this->subAddressData->getRawData();
    }

    protected function constructFormattedData() {
      return $this->isdnAddressData->getFormattedData().'/'.$this->subAddressData->getFormattedData();
    }

    public function isdnAddress($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->isdnAddressData;
      } else {
        $this->isdnAddressData = $newValue instanceof CharacterString ? $newValue : new CharacterString($newValue);
        $result = $this;
      }
      return $result;
    }

    public function subAddress($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->subAddressData;
      } else {
        $this->subAddressData = $newValue instanceof CharacterString ? $newValue : new CharacterString($newValue);
        $result = $this;
      }
      return $result;
    }

  }
