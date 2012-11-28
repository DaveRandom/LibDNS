<?php

  namespace DaveRandom\LibDNS\Messages;

  use \DaveRandom\LibDNS\Message;

  class Response extends Message {

    protected $type = 1;

    protected $compression = TRUE;

  }
