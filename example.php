<?php

  // Makes a simple A record lookup query for google.com

  function __autoload($className) {
    require str_replace('\\', '/', preg_replace('#^\\\\?DaveRandom\\\\DNS#i', 'src', $className)).'.php';
  }

  $queryDomain = 'google.com';
  $serverIP    = '8.8.8.8';

  use \DaveRandom\DNS\Packet;
  use \DaveRandom\DNS\Records\Query;
  use \DaveRandom\DNS\Messages\Request;
  use \DaveRandom\DNS\Messages\Response;

  // Define the request
  $request = new Request;
  $questionRecord = new Query($queryDomain, Query::RECORDTYPE_A, Query::RECORDCLASS_IN);
  $request->addQuestionRecord($questionRecord);
  $request->isRecursionDesired(TRUE);

  // Create the client socket
  $socket = stream_socket_client("udp://$serverIP:53");

  // Send the request
  stream_socket_sendto($socket, $request->getRawData());

  // Wait for the response
  $r = array($socket);
  $w = $e = array();
  stream_select($r, $w, $e, NULL);

  // Load the response
  $packet = new Packet(fread($socket, 512));
  $response = new Response;
  $response->loadPacket($packet);

  // Display the results
  foreach ($response->getAnswerRecords() as $record) {
    echo $record->getData()."\n";
  }
