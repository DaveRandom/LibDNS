<?php

  // Makes a simple A record lookup query for google.com

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\Records\Query;
  use \DaveRandom\LibDNS\Messages\Request;
  use \DaveRandom\LibDNS\Messages\Response;

  function __autoload($className) {
    include str_replace('\\', '/', preg_replace('#^\\\\?DaveRandom#i', '../src', $className)).'.php';
  }

  $queryDomain = 'google.com';
  $serverIP    = '8.8.8.8';

  // Define the request
  $request = new Request;
  $questionRecord = new Query($queryDomain);
  $request->addQuestionRecord($questionRecord);
  $request->isRecursionDesired(TRUE);

  // Create the client socket
  $socket = stream_socket_client("udp://$serverIP:53");

  // Send the request
  stream_socket_sendto($socket, $request->writeToPacket());

  // Wait for the response
  $r = array($socket);
  $w = $e = array();
  stream_select($r, $w, $e, NULL);

  // Load the response
  $packet = new Packet(fread($socket, 512));
  $response = new Response;
  $response->loadFromPacket($packet);

  // Display the results
  foreach ($response->getAnswerRecords() as $record) {
    echo $record->getData()."\n";
  }
