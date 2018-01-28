<?php
/**
 * Makes a simple A record lookup query and outputs the results
 */

namespace DaveRandom\LibDNS\Examples;

use DaveRandom\LibDNS\Protocol\Decoding\Decoder;
use DaveRandom\LibDNS\Protocol\Encoding\Encoder;
use DaveRandom\LibDNS\Protocol\Messages\MessageResponseCodes;
use DaveRandom\LibDNS\Protocol\Messages\Query;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceData\SOA;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\Network\DomainName;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/includes/functions.php';

// Config
const NAME = 'github.com';

echo "\n" . NAME . ":\n";

// Create question record
$question = new QuestionRecord(DomainName::createFromString(NAME), ResourceQTypes::SOA);

// Create query message
$query = new Query([$question]);

// Encode query message
$requestPacket = (new Encoder)->encode($query);

// Send query and wait for response
try {
    $responsePacket = send_query_to_server($requestPacket);
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
    $data = $record->getData();

    if ($data instanceof SOA) {
        echo "  {
    Primary Name Server : {$data->getMasterServerName()}
    Responsible Mail    : {$data->getResponsibleMailAddress()}
    Serial              : {$data->getSerial()}
    Refresh             : {$data->getRefreshInterval()}
    Retry               : {$data->getRetryInterval()}
    Expire              : {$data->getExpireTimeout()}
    TTL                 : {$data->getTtl()}
  }\n";
    }
}
