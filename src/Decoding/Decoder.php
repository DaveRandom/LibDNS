<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Decoding;

use DaveRandom\LibDNS\Messages\Message;
use DaveRandom\LibDNS\Messages\MessageFlags;
use DaveRandom\LibDNS\Messages\Query;
use DaveRandom\LibDNS\Messages\Response;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceRecord;
use function DaveRandom\LibDNS\decode_domain_name;

final class Decoder
{
    private $resourceDataDecoder;

    public function __construct()
    {
        $this->resourceDataDecoder = new ResourceDataDecoder();
    }

    /**
     * Decode the header section of the message
     *
     * @param DecodingContext $ctx
     * @param Message $message
     * @throws \UnexpectedValueException When the header section is invalid
     */
    private function decodeHeader(DecodingContext $ctx)
    {
        $header = $ctx->unpack('nid/nmeta/nqd/nan/nns/nar', 12);

        $ctx->messageId = $header['id'];
        $ctx->messageOpCode = ($header['meta'] & 0b0111100000000000) >> 11;
        $ctx->messageResponseCode = $header['meta'] & 0b0000000000001111;
        $ctx->messageFlags = $header['meta'] & Message::FLAGS_MASK;
        $ctx->expectedQuestionRecords = $header['qd'];
        $ctx->expectedAnswerRecords = $header['an'];
        $ctx->expectedAuthorityRecords = $header['ns'];
        $ctx->expectedAdditionalRecords = $header['ar'];
    }

    private function decodeQuestionRecord(DecodingContext $ctx): QuestionRecord
    {
        $name = decode_domain_name($ctx);
        $meta = $ctx->unpack('ntype/nclass', 4);

        return new QuestionRecord($name, $meta['type'], $meta['class']);
    }

    /**
     * Decode a resource record
     *
     * @param DecodingContext $decodingContext
     * @return \DaveRandom\LibDNS\Records\ResourceRecord
     * @throws \UnexpectedValueException When the record is invalid
     * @throws \InvalidArgumentException When a type subtype is unknown
     */
    private function decodeResourceRecord(DecodingContext $ctx): ResourceRecord
    {
        $name = decode_domain_name($ctx);
        $meta = $ctx->unpack('ntype/nclass/Nttl/nlength', 10);

        $resourceData = $this->resourceDataDecoder->decode($ctx, $meta['type'], $meta['length']);

        return new ResourceRecord($name, $meta['type'], $meta['class'], $meta['ttl'], $resourceData);
    }

    /**
     * Decode a Message from raw network data
     *
     * @param string $data The data string to decode
     * @return \DaveRandom\LibDNS\Messages\Message
     * @throws \UnexpectedValueException When the packet data is invalid
     * @throws \InvalidArgumentException When a type subtype is unknown
     */
    public function decode(string $data, int $offset = 0): Message
    {
        $ctx = new DecodingContext($data, $offset);

        $this->decodeHeader($ctx);

        $questionRecords = [];
        for ($i = 0; $i < $ctx->expectedQuestionRecords; $i++) {
            $questionRecords[] = $this->decodeQuestionRecord($ctx);
        }

        if (!($ctx->messageFlags & MessageFlags::IS_RESPONSE)) {
            return new Query($questionRecords, $ctx->messageId, $ctx->messageFlags, $ctx->messageOpCode);
        }

        $answerRecords = [];
        for ($i = 0; $i < $ctx->expectedAnswerRecords; $i++) {
            $answerRecords[] = $this->decodeResourceRecord($ctx);
        }

        $authorityRecords = [];
        for ($i = 0; $i < $ctx->expectedAuthorityRecords; $i++) {
            $authorityRecords[] = $this->decodeResourceRecord($ctx);
        }

        $additionalRecords = [];
        for ($i = 0; $i < $ctx->expectedAdditionalRecords; $i++) {
            $additionalRecords[] = $this->decodeResourceRecord($ctx);
        }

        return new Response(
            $ctx->messageId, $ctx->messageFlags, $ctx->messageOpCode, $ctx->messageResponseCode,
            $questionRecords, $answerRecords, $authorityRecords, $additionalRecords
        );
    }
}
