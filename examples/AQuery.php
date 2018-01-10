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

use DaveRandom\LibDNS\Messages\MessageFactory;
use DaveRandom\LibDNS\Messages\MessageTypes;
use DaveRandom\LibDNS\Records\QuestionFactory;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\LibDNS\Encoder\EncoderFactory;
use DaveRandom\LibDNS\Decoder\DecoderFactory;

// Config
$queryName      = 'faß.de';
$serverIP       = '8.8.8.8';
$requestTimeout = 3;

require __DIR__ . '/../vendor/autoload.php';

// Create question record
$question = (new QuestionFactory)->create(ResourceQTypes::A);
$question->setName($queryName);

// Create request message
$request = (new MessageFactory)->create(MessageTypes::QUERY);
$request->getQuestionRecords()->add($question);
$request->isRecursionDesired(true);

// Encode request message
$encoder = (new EncoderFactory)->create();
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

// Decode response message
$decoder = (new DecoderFactory)->create();
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
