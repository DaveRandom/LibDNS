<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\CharacterString;

  class HINFO extends Vector {

    private $cpuData;
    private $osData;

    public static function createFromPacket(Packet $packet, $dataLength = NULL) {
      $cpu = CharacterString::createFromPacket($packet);
      $os = CharacterString::createFromPacket($packet);
      return new self($cpu, $os);
    }

    public function __construct($cpu, $os) {
      $this->cpuData = $cpu instanceof CharacterString ? $cpu : new CharacterString($cpu);
      $this->osData = $os instanceof CharacterString ? $os : new CharacterString($os);
    }

    protected function constructRawData() {
      return $this->cpuData->getRawData().$this->osData->getRawData();
    }

    protected function constructFormattedData() {
      return $this->cpuData->getFormattedData().'/'.$this->osData->getFormattedData();
    }

    public function cpu($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->cpuData;
      } else {
        $this->cpuData = $newValue instanceof CharacterString ? $newValue : new CharacterString($newValue);
        $result = $this;
      }
      return $result;
    }

    public function os($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->osData;
      } else {
        $this->osData = $newValue instanceof CharacterString ? $newValue : new CharacterString($newValue);
        $result = $this;
      }
      return $result;
    }

  }
