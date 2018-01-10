<?php
/**
 * Makes a simple A record lookup query and outputs the results
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Examples
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 1.0.0
 */
namespace DaveRandom\LibDNS\Examples;

use DaveRandom\LibDNS\Decoder\DecoderFactory;
use DaveRandom\LibDNS\Encoder\Encoder;
use DaveRandom\LibDNS\Messages\Message;
use DaveRandom\LibDNS\Messages\MessageTypes;
use DaveRandom\LibDNS\Records\Question;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager;

// Config
$queryName      = 'google.com';
$serverIP       = '8.8.8.8';
$requestTimeout = 3;

require __DIR__ . '/../vendor/autoload.php';

// Create question record
$question = new Question(ResourceQTypes::SOA);
$question->setName($queryName);

// Create request message
$request = new Message(MessageTypes::QUERY);
$request->getQuestionRecords()->add($question);
$request->isRecursionDesired(true);

// Encode request message
$encoder = new Encoder();
$requestPacket = $encoder->encode($request);

echo "\n" . $queryName . ":\n";

// Send request
$socket = stream_socket_client("udp://$serverIP:53");
stream_socket_sendto($socket, $requestPacket);
$r = [$socket];
$w = $e = [];
if (!stream_select($r, $w, $e, $requestTimeout)) {
    echo "    Request timeout.\n";
    exit;
}

// Create type definition manager for custom manipulation
$typeDefs = new TypeDefinitionManager;
$typeDefs->getTypeDefinition(ResourceTypes::SOA)->setToStringFunction(function($mname, $rname, $serial, $refresh, $retry, $expire, $minimum) {
    return <<<DATA
{
    Primary Name Server : $mname
    Responsible Mail    : $rname
    Serial              : $serial
    Refresh             : $refresh
    Retry               : $retry
    Expire              : $expire
    Default TTL         : $minimum
}
DATA;
});

// Decode response message
$decoder = (new DecoderFactory)->create($typeDefs);
$responsePacket = fread($socket, 512);
$response = $decoder->decode($responsePacket);

// Handle response
if ($response->getResponseCode() !== 0) {
    echo "    Server returned error code " . $response->getResponseCode() . ".\n";
    exit;
}

$answers = $response->getAnswerRecords();
if (count($answers)) {
    foreach ($response->getAnswerRecords() as $record) {
        /** @var \DaveRandom\LibDNS\Records\Resource $record */
        echo "    " . $record->getData() . "\n";
    }
} else {
    echo "    Not found.\n";
}
