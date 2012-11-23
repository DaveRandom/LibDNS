<?php

  namespace DaveRandom\DNS\DataTypes;

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\DataType;

  class Vector extends DataType {

    protected $type = 99;

    public function __construct($data) {}

    public function getRawData() {
      return $this->constructRawData();
    }

    public function getFormattedData() {
      return $this->constructFormattedData();
    }

  }
