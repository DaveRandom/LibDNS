<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Protocol\Decoding;

use DaveRandom\LibDNS\Protocol\Messages\Message;
use DaveRandom\LibDNS\Protocol\Messages\MessageFlags;
use DaveRandom\LibDNS\Protocol\Messages\Query;
use DaveRandom\LibDNS\Protocol\Messages\Response;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceRecord;
use function DaveRandom\LibDNS\decode_domain_name;

final class Decoder
{
    private $resourceDataDecoder;

    public function __construct(ResourceDataDecoder $resourceDataDecoder = null)
    {
        $this->resourceDataDecoder = $resourceDataDecoder ?? new ResourceDataDecoder();
    }

    private function decodeHeader(Context $ctx)
    {
        $header = $ctx->unpack('nid/nmeta/nqd/nan/nns/nar', 12);

        $ctx->messageId = $header['id'];
        $ctx->messageOpCode = ($header['meta'] & 0b0111100000000000) >> 11;
        $ctx->messageResponseCode = $header['meta'] & 0b0000000000001111;
        /** @noinspection PhpInternalEntityUsedInspection */
        $ctx->messageFlags = $header['meta'] & Message::FLAGS_MASK;
        $ctx->expectedQuestionRecords = $header['qd'];
        $ctx->expectedAnswerRecords = $header['an'];
        $ctx->expectedAuthorityRecords = $header['ns'];
        $ctx->expectedAdditionalRecords = $header['ar'];
    }

    private function decodeQuestionRecord(Context $ctx): QuestionRecord
    {
        $name = decode_domain_name($ctx);
        $meta = $ctx->unpack('ntype/nclass', 4);

        return new QuestionRecord($name, $meta['type'], $meta['class']);
    }

    private function decodeResourceRecord(Context $ctx): ResourceRecord
    {
        $name = decode_domain_name($ctx);
        $meta = $ctx->unpack('ntype/nclass/Nttl/nlength', 10);

        $resourceData = $this->resourceDataDecoder->decode($ctx, $meta['type'], $meta['length']);

        return new ResourceRecord($name, $meta['type'], $meta['class'], $meta['ttl'], $resourceData);
    }

    public function decode(string $data, int $offset = 0): Message
    {
        $ctx = new Context($data, $offset);

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
