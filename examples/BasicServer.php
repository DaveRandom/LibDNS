<?php

  // Makes a simple A record lookup query for google.com

  use \DaveRandom\LibDNS\Packet;
  use \DaveRandom\LibDNS\Messages\Request;
  use \DaveRandom\LibDNS\Messages\Response;
  use \DaveRandom\LibDNS\Records\Resource;
  use \DaveRandom\LibDNS\DataTypes\IPv4Address;
  use \DaveRandom\LibDNS\DataTypes\Vectors\SOA;

  function __autoload($className) {
    include str_replace('\\', '/', preg_replace('#^\\\\?DaveRandom#i', '../src', $className)).'.php';
  }

  $serverIP    = '0.0.0.0';
  $zones = array(
    'example1.com' => array(
      'soa' => array(
        'mname' => 'ns.myhost.com',
        'rname' => 'domains-myhost.com',
        'serial' => 10,
        'refresh' => 7200,
        'retry' => 600,
        'expire' => 3600,
        'minimum' => 60
      ),
      'a' => array(
        '@' => '11.22.33.44',
        'www' => '11.22.33.44',
        'mail' => '44.33.22.11'
      )
    ),

    'example2.net' => array(
      'soa' => array(
        'mname' => 'ns.myhost.com',
        'rname' => 'domains-myhost.com',
        'serial' => 20,
        'refresh' => 14400,
        'retry' => 300,
        'expire' => 7200,
        'minimum' => 30
      ),
      'a' => array(
        '@' =>'99.88.77.66',
        'www' => '99.88.77.66',
        'mail' => '99.88.88.99',
        'sub.www' => '123.45.67.89'
      )
    )
  );

  // Create listen socket
  $socket = stream_socket_server("udp://$serverIP:53", $errNo, $errStr, STREAM_SERVER_BIND);

  // Main server loop
  while (TRUE) {

    // Wait for a request
    $r = array($socket);
    $w = $e = array();
    stream_select($r, $w, $e, NULL);

    // Load the recieved packet
    $data = stream_socket_recvfrom($socket, 512, 0, $clientAddress);
    echo "Received request from client $clientAddress\n";
    $packet = new Packet($data);

    // Create request/response message objects
    $request = new Request;
    $request->loadFromPacket($packet);
    $response = new Response($request->getID());

    // Loop request message questions and lookup data from $zones
    foreach ($request->getQuestionRecords() as $question) {

      $name = $question->getName()->getFormattedData();
      $queryType = $question->getType();
      $answer = NULL;

      // Split subdomain tokens from root
      $rootTokens = $question->getName()->getTokens();
      $subTokens = array();
      while (count($rootTokens) > 2) {
        $subTokens[] = array_shift($rootTokens);
      }
      $rootName = implode('.', $rootTokens);
      $subName = implode('.', $subTokens);

      // Lookup record
      if (isset($zones[$rootName])) {

        $zone = $zones[$rootName];
        $ttl = 3600;

        switch ($question->getType()) {

          case Resource::TYPE_A:
            $queryType = 'A';
            if ($subName === '') { // Request is for root of domain
              $subName = '@';
            }
            if (isset($zone['a'][$subName])) {
              $result = new IPv4Address($zone['a'][$subName]);
              $answer = new Resource($question->getName(), Resource::TYPE_A, Resource::CLASS_IN, $ttl, $result);
            } else {
              $result = 'Not found';
            }
            break;

          case Resource::TYPE_SOA:
            $queryType = 'SOA';
            $result = new SOA(
              $zone['soa']['mname'], $zone['soa']['rname'],
              $zone['soa']['serial'],
              $zone['soa']['refresh'],
              $zone['soa']['retry'],
              $zone['soa']['expire'],
              $zone['soa']['minimum']
            );
            $answer = new Resource($question->getName(), Resource::TYPE_SOA, Resource::CLASS_IN, $ttl, $result);
            break;

          default:
            $result = 'Not implemented';
            break;

        }

        $response->addQuestionRecord($question);
        if ($answer) {
          $response->addAnswerRecord($answer);
        }

      } else {
        $result = 'Unknown root name';
      }

      echo "$name; Type: $queryType; Result: $result\n";
    }

    // Send response
    stream_socket_sendto($socket, $response->writeToPacket(), 0, $clientAddress);
    
    echo "-------\n";
  }
