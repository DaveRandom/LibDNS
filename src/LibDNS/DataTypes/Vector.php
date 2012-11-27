<?php

  namespace DaveRandom\LibDNS\DataTypes;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\DataType;

  abstract class Vector extends DataType {

    public function __construct($data) {}

    public function getRawData() {
      return $this->constructRawData();
    }

    public function getFormattedData() {
      return $this->constructFormattedData();
    }

    abstract protected function constructRawData();

    abstract protected function constructFormattedData();

  }
