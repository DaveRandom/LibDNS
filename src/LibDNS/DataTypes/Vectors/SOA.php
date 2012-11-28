<?php

  namespace DaveRandom\LibDNS\DataTypes\Vectors;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\DataTypes\Vector;
  use \DaveRandom\LibDNS\DataTypes\DomainName;
  use \DaveRandom\LibDNS\DataTypes\Long;

  class SOA extends Vector {

    private $mNameData;
    private $rMailData;
    private $serialData;
    private $refreshData;
    private $retryData;
    private $expireData;
    private $minimumData;

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      $mName = (new DomainName)->loadFromPacket($packet);
      $rMail = (new DomainName)->loadFromPacket($packet);
      $serial = (new Long)->loadFromPacket($packet);
      $refresh = (new Long)->loadFromPacket($packet);
      $retry = (new Long)->loadFromPacket($packet);
      $expire = (new Long)->loadFromPacket($packet);
      $minimum = (new Long)->loadFromPacket($packet);
      $this->__construct($mName, $rMail, $serial, $refresh, $retry, $expire, $minimum);
      return $this;
    }

    public function __construct($mName = NULL, $rMail = NULL, $serial = NULL, $refresh = NULL, $retry = NULL, $expire = NULL, $minimum = NULL) {
      $this->mNameData = $mName instanceof DomainName ? $mName : new DomainName($mName);
      $this->rMailData = $rMail instanceof DomainName ? $rMail : new DomainName($rMail);
      $this->serialData = $serial instanceof Long ? $serial : new Long($serial);
      $this->refreshData = $refresh instanceof Long ? $refresh : new Long($refresh);
      $this->retryData = $retry instanceof Long ? $retry : new Long($retry);
      $this->expireData = $expire instanceof Long ? $expire : new Long($expire);
      $this->minimumData = $minimum instanceof Long ? $minimum : new Long($minimum);
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock(TRUE)
        ->writeDomainName($this->mNameData)
        ->writeDomainName($this->rMailData)
        ->write($this->serialData->getRawData())
        ->write($this->refreshData->getRawData())
        ->write($this->retryData->getRawData())
        ->write($this->expireData->getRawData())
        ->write($this->minimumData->getRawData());
    }

    protected function constructRawData() {
      return $this->mNameData->getRawData()
           . $this->rMailData->getRawData()
           . $this->serialData->getRawData()
           . $this->refreshData->getRawData()
           . $this->retryData->getRawData()
           . $this->expireData->getRawData()
           . $this->minimumData->getRawData();
    }

    protected function constructFormattedData() {
      return "{\n"
           . "  Primary Name Server      = " . $this->mNameData . "\n"
           . "  Responsible Mail Address = " . $this->rMailData . "\n"
           . "  Serial                   = " . $this->serialData . "\n"
           . "  Refresh                  = " . $this->refreshData . "\n"
           . "  Retry                    = " . $this->retryData . "\n"
           . "  Expire                   = " . $this->expireData . "\n"
           . "  Minimum                  = " . $this->minimumData . "\n"
           . "}";
    }

    public function primaryNameServer($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->mNameData;
      } else {
        $this->mNameData = $newValue instanceof DomainName ? $newValue : new DomainName($newValue);
        $result = $this;
      }
      return $result;
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

    public function serial($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->serialData;
      } else {
        $this->serialData = $newValue instanceof Long ? $newValue : new Long($newValue);
        $result = $this;
      }
      return $result;
    }

    public function refresh($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->refreshData;
      } else {
        $this->refreshData = $newValue instanceof Long ? $newValue : new Long($newValue);
        $result = $this;
      }
      return $result;
    }

    public function retry($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->retryData;
      } else {
        $this->retryData = $newValue instanceof Long ? $newValue : new Long($newValue);
        $result = $this;
      }
      return $result;
    }

    public function expire($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->expireData;
      } else {
        $this->expireData = $newValue instanceof Long ? $newValue : new Long($newValue);
        $result = $this;
      }
      return $result;
    }

    public function minimum($newValue = NULL) {
      if ($newValue === NULL) {
        $result = $this->minimumData;
      } else {
        $this->minimumData = $newValue instanceof Long ? $newValue : new Long($newValue);
        $result = $this;
      }
      return $result;
    }

  }
