<?php

  namespace DaveRandom\LibDNS;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;

  abstract class DataType {

    public function __toString() {
      return (string) $this->getFormattedData();
    }

    public function writeToPacket(PacketBuilder $packetBuilder, $withLengthWord = FALSE) {
      $packetBuilder
        ->addWriteBlock($withLengthWord)
        ->write($this->getRawData());
    }

    abstract public function __construct($data);

    abstract public function getRawData();

    abstract public function getFormattedData();

  }
