<?php
/**
 * Makes a simple A record lookup query and outputs the results
 */

namespace DaveRandom\LibDNS\Examples;

use DaveRandom\LibDNS\Decoding\Decoder;
use DaveRandom\LibDNS\Encoding\Encoder;
use DaveRandom\LibDNS\Messages\MessageResponseCodes;
use DaveRandom\LibDNS\Messages\Query;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceData\SOA;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\Network\DomainName;

// Config
$queryName      = 'github.com';
$serverIP       = '8.8.8.8';
$requestTimeout = 3;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/includes/functions.php';

// Create question record
$question = new QuestionRecord(DomainName::createFromString($queryName), ResourceQTypes::SOA);

// Create query message
$query = new Query([$question]);

// Encode query message
$requestPacket = (new Encoder)->encode($query);

echo "\n{$queryName}:\n";

// Send query and wait for response
try {
    $responsePacket = send_query_to_server($requestPacket, $serverIP, $requestTimeout);
} catch (\RuntimeException $e) {
    exit("  {$e->getMessage()}\n");
}

// Decode response message
$response = (new Decoder)->decode($responsePacket);

// Handle response
if ($response->getResponseCode() !== MessageResponseCodes::NO_ERROR) {
    $errorName = MessageResponseCodes::parseValue($response->getResponseCode());
    exit("  Server returned error code: {$response->getResponseCode()}: {$errorName}\n");
}

$answers = $response->getAnswerRecords();

if (count($answers) === 0) {
    exit("  Not found\n");
}

foreach ($answers as $record) {
    $responsePacket = $record->getData();

    if ($responsePacket instanceof SOA) {
        echo "  {
    Primary Name Server : {$responsePacket->getMasterServerName()}
    Responsible Mail    : {$responsePacket->getResponsibleMailAddress()}
    Serial              : {$responsePacket->getSerial()}
    Refresh             : {$responsePacket->getRefreshInterval()}
    Retry               : {$responsePacket->getRetryInterval()}
    Expire              : {$responsePacket->getExpireTimeout()}
    TTL                 : {$responsePacket->getTtl()}
  }\n";
    }
}
