<?php

  namespace DaveRandom\DNS\DataTypes\Vectors;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataTypes\Vector;
  use \DaveRandom\DNS\DataTypes\Short;
  use \DaveRandom\DNS\DataTypes\DomainName;

  class MX extends Vector {

    private $preferenceData;
    private $exchangeData;

    public static function createFromPacket(Packet $packet, $dataLength = NULL, $type = NULL) {
      $preference = Short::createFromPacket($packet);
      $exchange = DomainName::createFromPacket($packet);
      return new self($preference, $exchange);
    }

    public function __construct($preference, $exchange) {
      $this->preferenceData = $preference instanceof Short ? $preference : new Short($preference);
      $this->exchangeData = $exchange instanceof DomainName ? $exchange : new DomainName($exchange);
    }

    protected function constructRawData() {
      return $this->preferenceData->getRawData().$this->exchangeData->getRawData();
    }

    protected function constructFormattedData() {
      return $this->preferenceData->getFormattedData().' '.$this->exchangeData->getFormattedData();
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

    public function exchange($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->exchangeData;
      } else {
        $this->exchangeData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
    }

  }
