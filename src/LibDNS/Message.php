<?php

  namespace DaveRandom\LibDNS;

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\PacketBuilder\PacketBuilder;
  use \DaveRandom\LibDNS\Records\Query;
  use \DaveRandom\LibDNS\Records\Resource;

  abstract class Message {

    const MESSAGETYPE_QUERY    = 0;
    const MESSAGETYPE_RESPONSE = 1;

    const OPCODE_QUERY  = 0;
    const OPCODE_IQUERY = 1;
    const OPCODE_STATUS = 2;

    const RESPONSECODE_NOERROR        = 0;
    const RESPONSECODE_FORMATERROR    = 1;
    const RESPONSECODE_SERVERERROR    = 2;
    const RESPONSECODE_NAMEERROR      = 3;
    const RESPONSECODE_NOTIMPLEMENTED = 4;
    const RESPONSECODE_REFUSED        = 5;

    private $responseMessages = array(
      0 => 'No error',
      1 => 'Format error',
      2 => 'Server error',
      3 => 'Name error',
      4 => 'Not implemented',
      5 => 'Refused'
    );

    protected $id = 0;
    protected $type;
    protected $opCode;
    protected $authoritative = FALSE;
    protected $truncated = FALSE;
    protected $recursionDesired = FALSE;
    protected $recursionAvailable = FALSE;
    protected $responseCode = 0;

    protected $questionRecords = array();
    protected $answerRecords = array();
    protected $nameServerRecords = array();
    protected $additionalRecords = array();

    protected $compression = TRUE;

    public function __construct($id = NULL) {
      if ($id !== NULL) {
        $this->id = (int) $id;
      }
    }

    public function loadFromPacket(Packet $packet) {
      if (FALSE === $header = $packet->read(12)) {
        throw new \InvalidArgumentException('Malformed packet');
      }
      list($id, $meta, $qdCount, $anCount, $nsCount, $arCount) = array_values(unpack('n*', $header));

      $this->id = $id;

      $this->type = ($meta >> 15) & 0x0001;
      $this->opCode = ($meta >> 11) & 0x000F;
      $this->authoritative = (bool) (($meta >> 10) & 0x0001);
      $this->truncated = (bool) (($meta >> 9) & 0x0001);
      $this->recursionDesired = (bool) (($meta >> 8) & 0x0001);
      $this->recursionAvailable = (bool) (($meta >> 7) & 0x0001);
      $this->responseCode = $meta & 0x000F;

      for ($i = 0; $i < $qdCount; $i++) {
        $this->questionRecords[] = Query::createFromPacket($packet);
      }

      for ($i = 0; $i < $anCount; $i++) {
        $this->answerRecords[] = Resource::createFromPacket($packet);
      }

      for ($i = 0; $i < $nsCount; $i++) {
        $this->nameServerRecords[] = Resource::createFromPacket($packet);
      }

      for ($i = 0; $i < $arCount; $i++) {
        $this->additionalRecords[] = Resource::createFromPacket($packet);
      }

      return $this;
    }
    public function writeToPacket(Packet $packet = NULL) {
      $packetBuilder = new PacketBuilder($this->compression);
      $this->truncated = FALSE;

      $headerBlock = $packetBuilder->addWriteBlock();
      $headerBlock->write("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00");

      try {
        foreach ($this->questionRecords as $record) {
          $record->writeToPacket($packetBuilder);
        }
        foreach ($this->answerRecords as $record) {
          $record->writeToPacket($packetBuilder);
        }
        foreach ($this->nameServerRecords as $record) {
          $record->writeToPacket($packetBuilder);
        }
        foreach ($this->additionalRecords as $record) {
          $record->writeToPacket($packetBuilder);
        }
      } catch (\Exception $e) {
        $this->truncated = TRUE;
      }

      $meta  = $this->type << 15;
      $meta |= $this->opCode << 11;
      $meta |= ((int) $this->authoritative) << 10;
      $meta |= ((int) $this->truncated) << 9;
      $meta |= ((int) $this->recursionDesired) << 8;
      $meta |= $this->responseCode;

      $qdCount = count($this->questionRecords);
      $anCount = count($this->answerRecords);
      $nsCount = count($this->nameServerRecords);
      $arCount = count($this->additionalRecords);
      $headerBlock->overwrite(pack('n*', $this->id, $meta, $qdCount, $anCount, $nsCount, $arCount));

      return $packetBuilder->write($packet);
    }

    public function isTruncated() {
      return $this->truncated;
    }

    public function getQuestionRecords() {
      return $this->questionRecords;
    }
    public function addQuestionRecord(Records\Query $record) {
      foreach ($this->questionRecords as $key => $val) {
        if ($val === $record) {
          return $record;
        }
      }
      $this->questionRecords[] = $record;
      return $record;
    }
    public function removeQuestionRecord(Records\Query $record) {
      foreach ($this->questionRecords as $key => $val) {
        if ($val === $record) {
          array_splice($this->questionRecords, $key, 1);
          return $record;
        }
      }
    }

    public function getAnswerRecords() {
      return $this->answerRecords;
    }
    public function addAnswerRecord(Records\Resource $record) {
      foreach ($this->answerRecords as $key => $val) {
        if ($val === $record) {
          return $record;
        }
      }
      $this->answerRecords[] = $record;
      return $record;
    }
    public function removeAnswerRecord(Records\Resource $record) {
      foreach ($this->answerRecords as $key => $val) {
        if ($val === $record) {
          array_splice($this->answerRecords, $key, 1);
          return $record;
        }
      }
    }

    public function getNameServerRecords() {
      return $this->nameServerRecords;
    }
    public function addNameServerRecord(Records\Resource $record) {
      foreach ($this->nameServerRecords as $key => $val) {
        if ($val === $record) {
          return $record;
        }
      }
      $this->nameServerRecords[] = $record;
      return $record;
    }
    public function removeNameServerRecord(Records\Resource $record) {
      foreach ($this->nameServerRecords as $key => $val) {
        if ($val === $record) {
          array_splice($this->nameServerRecords, $key, 1);
          return $record;
        }
      }
    }

    public function getAdditionalRecords() {
      return $this->additionalRecords;
    }
    public function addAdditionalRecord(Records\Resource $record) {
      foreach ($this->additionalRecords as $key => $val) {
        if ($val === $record) {
          return $record;
        }
      }
      $this->additionalRecords[] = $record;
      return $record;
    }
    public function removeAdditionalRecord(Records\Resource $record) {
      foreach ($this->additionalRecords as $key => $val) {
        if ($val === $record) {
          array_splice($this->additionalRecords, $key, 1);
          return $record;
        }
      }
    }

    public function enableCompression($newValue) {
      $this->compression = (bool) $newValue;
      return $this;
    }

    public function getID() {
      return $this->id;
    }
    public function setID($newValue) {
      if (!is_scalar($newValue) && $newValue !== NULL) {
        throw new \InvalidArgumentException('Invalid data type');
      }
      $newValue = (int) $newValue;
      if ($newValue < 0 || $newValue > 65535) {
        throw new \InvalidArgumentException('Value outside acceptable range for an unsigned short integer');
      }
      $this->id = $newValue;
      return $this;
    }

    public function getType() {
      return $this->type;
    }

    public function isAuthoritative($newValue = NULL) {
      $oldValue = $this->authoritative;
      if ($newValue !== NULL) {
        $this->authoritative = (bool) $newValue;
      }
      return $oldValue;
    }

    public function isRecursionDesired($newValue = NULL) {
      $oldValue = $this->recursionDesired;
      if ($newValue !== NULL) {
        $this->recursionDesired = (bool) $newValue;
      }
      return $oldValue;
    }
    public function isRecursionAvailable($newValue = NULL) {
      $oldValue = $this->recursionAvailable;
      if ($newValue !== NULL) {
        $this->recursionAvailable = (bool) $newValue;
      }
      return $oldValue;
    }

    public function getResponseCode() {
      return $this->responseCode;
    }
    public function setResponseCode($newValue = NULL) {
      if (!is_scalar($newValue) && $newValue !== NULL) {
        throw new \InvalidArgumentException('Invalid data type');
      }
      $newValue = (int) $newValue;
      if ($newValue < 0 || $newValue > 5) {
        throw new \InvalidArgumentException('Value is not a recognised response code');
      }
      $this->responseCode = $newValue;
      return $this;
    }
    public function getErrorMessage() {
      return isset($this->responseMessages[$this->responseCode]) ? $this->responseMessages[$this->responseCode] : 'Unknown error';
    }

    public function getOpCode() {
      return $this->opCode;
    }
    public function setOpCode($newValue) {
      if (!is_scalar($newValue) && $newValue !== NULL) {
        throw new \InvalidArgumentException('Invalid data type');
      }
      $newValue = (int) $newValue;
      if ($newValue < 1 || $newValue > 3) {
        throw new \InvalidArgumentException('Value is not a recognised opcode');
      }
      $this->opCode = $newValue;
      return $this;
    }

  }
