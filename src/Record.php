<?php

  namespace DaveRandom\DNS;

  use \DaveRandom\DNS\DataTypes\DomainName;

  abstract class Record implements IEntity {

    protected $name;
    protected $type;
    protected $class;

    public function __construct($name, $type, $class) {
      if ($name instanceof DomainName) {
        $this->name = $name;
      } else {
        $this->name = new DomainName($name);
      }
      $this->type = $type;
      $this->class = $class;
    }

    public function getName() {
      return $this->name;
    }

    public function getType() {
      return $this->type;
    }

    public function getClass() {
      return $this->class;
    }

  }
