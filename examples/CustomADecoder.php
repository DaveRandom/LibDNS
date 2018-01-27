<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Examples;

use DaveRandom\LibDNS\Decoding\Decoder;
use DaveRandom\LibDNS\Decoding\ResourceDataDecoder;
use DaveRandom\LibDNS\DecodingContext;
use DaveRandom\LibDNS\Encoding\Encoder;
use DaveRandom\LibDNS\Messages\MessageResponseCodes;
use DaveRandom\LibDNS\Messages\Query;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPv4Address;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/includes/functions.php';

// Config
const NAME = 'github.com';

echo "\n" . NAME . ":\n";

// Create question record
$question = new QuestionRecord(DomainName::createFromString(NAME), ResourceQTypes::A);

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

$rrDecoder = new ResourceDataDecoder();
$rrDecoder->registerDecoder(ResourceTypes::A, function(DecodingContext $ctx): ResourceData  {
    return new class(new IPv4Address(...\array_values($ctx->unpack('C4', 4)))) implements ResourceData {
        private $address;

        public function __construct(IPv4Address $address)
        {
            $this->address = $address;
        }

        public function getTypeId(): int
        {
            return ResourceQTypes::A;
        }

        public function __toString(): string
        {
            return (string)$this->address;
        }
    };
});

// Decode response message
$response = (new Decoder($rrDecoder))->decode($responsePacket);

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
    echo (string)$record->getData() . "\n";
}
