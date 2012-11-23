<?php

  namespace DaveRandom\DNS;

  use \DaveRandom\DNS\Compression\Map;
  use \DaveRandom\DNS\Compression\Pointer;

  class Compressor extends Message {

    private $map;

    public function __construct(Message $message) {
      $this->id = $message->getID();
      $this->type = $message->getType();
      $this->opCode = $message->getOpCode();
      $this->authoritative = $message->isAuthoritative();
      $this->recursionDesired = $message->isRecursionDesired();
      $this->recursionAvailable = $message->isRecursionAvailable();
      $this->responseCode = $message->getResponseCode();

      $this->questionRecords = $message->getQuestionRecords();
      $this->answerRecords = $message->getAnswerRecords();
      $this->nameServerRecords = $message->getNameServerRecords();
      $this->additionalRecords = $message->getAdditionalRecords();
    }

    private function buildMap() {
      $this->map = new Map();
      
    }

    protected function buildRecordsetData($type, &$messageLength) {
    }

    protected function constructPacket() {
      $this->buildMap();
      parent::constructPacket();
    }

  }
