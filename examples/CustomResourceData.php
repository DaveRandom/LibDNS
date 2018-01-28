<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Examples;

use DaveRandom\LibDNS\Protocol\Decoding\Decoder;
use DaveRandom\LibDNS\Protocol\Decoding\ResourceDataDecoder;
use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\Encoding\Encoder;
use DaveRandom\LibDNS\Protocol\Encoding\ResourceDataEncoder;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Protocol\Messages\MessageResponseCodes;
use DaveRandom\LibDNS\Protocol\Messages\Query;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/includes/functions.php';

// Custom record class
class LOC implements ResourceData
{
    public $version;
    public $size;
    public $horizontalPrecision;
    public $verticalPrecision;
    public $latitude;
    public $longitude;
    public $altitude;

    public function __construct(int $version, int $size, int $hPrec, int $vPrec, int $lat, int $long, int $alt)
    {
        $this->version = $version;
        $this->size = $size;
        $this->horizontalPrecision = $hPrec;
        $this->verticalPrecision = $vPrec;
        $this->latitude = $lat;
        $this->longitude = $long;
        $this->altitude = $alt;
    }

    public function getTypeId(): int
    {
        return ResourceTypes::LOC;
    }
}

// Register custom encoder/decoder
$rrDecoder = new ResourceDataDecoder();
$rrDecoder->registerDecoder(ResourceTypes::LOC, function(DecodingContext $ctx): LOC  {
    return new LOC(...\array_values($ctx->unpack('C/C/C/C/N/N/N', 16)));
});

$rrEncoder = new ResourceDataEncoder();
$rrEncoder->registerEncoder(ResourceTypes::LOC, function(EncodingContext $ctx, LOC $record) {
    $ctx->appendData(\pack(
        'C4N3',
        $record->version,
        $record->size,
        $record->horizontalPrecision,
        $record->verticalPrecision,
        $record->latitude,
        $record->longitude,
        $record->altitude
    ));
});

// Create question record
$question = new QuestionRecord(DomainName::createFromString('geekatlas.com'), ResourceQTypes::LOC);

// Create query message
$query = new Query([$question]);

// Encode query message
$requestPacket = (new Encoder($rrEncoder))->encode($query);

// Send query and wait for response
try {
    $responsePacket = send_query_to_server($requestPacket);
} catch (\RuntimeException $e) {
    exit("  {$e->getMessage()}\n");
}

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
    var_dump($record->getData());
}
