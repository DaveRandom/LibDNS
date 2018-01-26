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

use DaveRandom\LibDNS\Decoding\Decoder;
use DaveRandom\LibDNS\Encoding\Encoder;
use DaveRandom\LibDNS\Messages\Query;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceData\A;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\Network\DomainName;

// Config
$queryName      = 'faß.de';
$serverIP       = '8.8.8.8';
$requestTimeout = 3;

require __DIR__ . '/../vendor/autoload.php';

// Create question record
$question = new QuestionRecord(DomainName::createFromString($queryName), ResourceQTypes::A);

// Create request message
$request = new Query(0, [$question]);

// Encode request message
$requestPacket = (new Encoder)->encode($request);

echo "\n" . $queryName . ":\n";

// Send request
$socket = stream_socket_client("udp://{$serverIP}:53");
stream_socket_sendto($socket, $requestPacket);
$r = [$socket];
$w = $e = [];
if (!stream_select($r, $w, $e, $requestTimeout)) {
    echo "    Request timeout\n";
    exit;
}

// Decode response message
$response = (new Decoder)->decode(fread($socket, 512));

// Handle response
if ($response->getResponseCode() !== 0) {
    echo "    Server returned error code: {$response->getResponseCode()}\n";
    exit;
}

$answers = $response->getAnswerRecords();

if (count($answers) === 0) {
    echo "    Not found\n";
    exit;
}

foreach ($answers as $record) {
    $data = $record->getData();

    if ($data instanceof A) {
        echo "    {$data->getAddress()}\n";
    }
}
