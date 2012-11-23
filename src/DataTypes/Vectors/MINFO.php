<?php

  namespace DaveRandom\DNS\DataTypes\Vectors;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataTypes\Vector;
  use \DaveRandom\DNS\DataTypes\DomainName;

  class MINFO extends Vector {

    private $rMailData;
    private $eMailData;

    public static function createFromPacket(Packet $packet, $dataLength = NULL, $type = NULL) {
      $rMail = DomainName::createFromPacket($packet);
      $eMail = DomainName::createFromPacket($packet);
      return new self($rMail, $eMail);
    }

    public function __construct($rMail, $eMail) {
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
