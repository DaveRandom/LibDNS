<?php

  namespace DaveRandom\LibDNS;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;

  abstract class DataType {

    public function __toString() {
      return (string) $this->getFormattedData();
    }

    public function loadFromPacket(Packet $packet, $dataLength = NULL) {
      if (FALSE === $data = $packet->read($dataLength)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      $this->__construct($data);
      return $this;
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock($withLengthWord)
        ->write($this->getRawData());
    }

    public function __construct($data = NULL) {
      if ($data !== NULL) {
        $this->setData($data);
      }
    }

    abstract public function getRawData();

    abstract public function getFormattedData();

    abstract public function setData($data);

  }
