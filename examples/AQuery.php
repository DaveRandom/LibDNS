<?php
/**
 * Makes a simple A record lookup query and outputs the results
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Examples
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */
namespace LibDNS\Examples;

use \LibDNS\Messages\MessageFactory,
    \LibDNS\Messages\MessageTypes,
    \LibDNS\Records\QuestionFactory,
    \LibDNS\Records\ResourceQTypes,
    \LibDNS\DataTypes\DataTypeFactory,
    \LibDNS\Encoder\EncoderFactory,
    \LibDNS\Decoder\DecoderFactory;

// Config
$queryDomain    = 'google.com';
$serverIP       = '8.8.8.8';
$requestTimeout = 3;

require __DIR__ . '/autoload.php';

$messageFactory = new MessageFactory;
$questionFactory = new QuestionFactory;
$dataTypeFactory = new DataTypeFactory;
$encoderFactory = new EncoderFactory;
$decoderFactory = new DecoderFactory;

// Create question record
$queryName = $dataTypeFactory->createDomainName($queryDomain);
$question = $questionFactory->create(ResourceQTypes::A);
$question->setName($queryName);

// Create request message
$request = $messageFactory->create(MessageTypes::QUERY);
$request->getQuestionRecords()->add($question);
$request->isRecursionDesired(true);

// Encode request message
$encoder = $encoderFactory->create();
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
$responsePacket = fread($socket, 512);
$decoder = $decoderFactory->create();
$response = $decoder->decode($responsePacket);

// Handle response
if ($response->getResponseCode() !== 0) {
    echo "    Server returned error code " . $response->getResponseCode() . ".\n";
    exit;
}

$answers = $response->getAnswerRecords();
if (count($answers)) {
    foreach ($response->getAnswerRecords() as $record) {
        echo "    " . $record->getData() . "\n";
    }
} else {
    echo "    Not found.\n";
}
