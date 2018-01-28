<?php

namespace LibDNS\Tests;

use DaveRandom\LibDNS\Decoding\Decoder;
use DaveRandom\LibDNS\Encoding\Encoder;
use DaveRandom\LibDNS\Messages\Message;
use DaveRandom\LibDNS\Messages\MessageFlags;
use DaveRandom\LibDNS\Messages\MessageOpCodes;
use DaveRandom\LibDNS\Messages\MessageResponseCodes;
use DaveRandom\LibDNS\Messages\Query;
use DaveRandom\LibDNS\Messages\Response;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceClasses;
use DaveRandom\LibDNS\Records\ResourceData\A;
use DaveRandom\LibDNS\Records\ResourceData\SOA;
use DaveRandom\LibDNS\Records\ResourceQTypes;
use DaveRandom\LibDNS\Records\ResourceRecord;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;
use PHPUnit\Framework\TestCase;

final class BigEndToEndTest extends TestCase
{
    public function testARecordQuestionCanBeEncodedIntoAPacket()
    {
        $queryName = 'google.com';

        $question = new QuestionRecord(DomainName::createFromString($queryName), ResourceQTypes::A);
        $query = new Query([$question]);
        $packet = (new Encoder)->encode($query);

        $this->assertPacketEquals('00000100000100000000000006676f6f676c6503636f6d0000010001', $packet);
    }

    public function testARecordResponseCanBeDecoded()
    {
        $responsePacket = hex2bin('00008180000100010000000006676f6f676c6503636f6d0000010001c00c000100010000012b0004d83ace6e');

        /** @var Response $response */
        $response = (new Decoder)->decode($responsePacket);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertMessageHeaderEquals(
            0,
            MessageOpCodes::QUERY,
            MessageResponseCodes::NO_ERROR,
            MessageFlags::IS_RESPONSE | MessageFlags::IS_RECURSION_DESIRED | MessageFlags::IS_RECURSION_AVAILABLE,
            1, 1, 0, 0, $response
        );

        $this->assertQuestionRecordHeaderEquals(
            'google.com',
            ResourceClasses::IN,
            ResourceTypes::A,
            $response->getQuestionRecords()[0]
        );

        $this->assertResourceRecordHeaderEquals(
            'google.com',
            ResourceClasses::IN,
            ResourceTypes::A,
            299,
            $response->getAnswerRecords()[0]
        );

        /** @var A $rData */
        $rData = $response->getAnswerRecords()[0]->getData();

        $this->assertInstanceOf(A::class, $rData);
        $this->assertSame('216.58.206.110', (string)$rData->getAddress());
    }

    public function testSoaRecordQuestionCanBeEncodedIntoAPacket()
    {
        $queryName = 'google.com';

        $question = new QuestionRecord(DomainName::createFromString($queryName), ResourceQTypes::SOA);
        $request = new Query([$question]);
        $requestPacket = (new Encoder)->encode($request);

        $this->assertPacketEquals(
            '00000100000100000000000006676f6f676c6503636f6d0000060001',
            $requestPacket
        );
    }

    public function testSoaRecordResponseCanBeDecoded()
    {
        $responsePacket = hex2bin('00008180000100010000000006676f6f676c6503636f6d0000060001c00c000600010000003b0026036e7331c00c09646e732d61646d696ec00c0accab9e0000038400000384000007080000003c');

        /** @var Response $response */
        $response = (new Decoder)->decode($responsePacket);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertMessageHeaderEquals(
            0,
            MessageOpCodes::QUERY,
            MessageResponseCodes::NO_ERROR,
            MessageFlags::IS_RESPONSE | MessageFlags::IS_RECURSION_DESIRED | MessageFlags::IS_RECURSION_AVAILABLE,
            1, 1, 0, 0, $response
        );

        $this->assertQuestionRecordHeaderEquals(
            'google.com',
            ResourceClasses::IN,
            ResourceTypes::SOA,
            $response->getQuestionRecords()[0]
        );

        $this->assertResourceRecordHeaderEquals(
            'google.com',
            ResourceClasses::IN,
            ResourceTypes::SOA,
            59,
            $response->getAnswerRecords()[0]
        );

        /** @var SOA $rData */
        $rData = $response->getAnswerRecords()[0]->getData();

        $this->assertInstanceOf(SOA::class, $rData);
        $this->assertSame('ns1.google.com', (string)$rData->getMasterServerName());
        $this->assertSame('dns-admin.google.com', (string)$rData->getResponsibleMailAddress());
        $this->assertSame(181185438, $rData->getSerial());
        $this->assertSame(900, $rData->getRefreshInterval());
        $this->assertSame(900, $rData->getRetryInterval());
        $this->assertSame(1800, $rData->getExpireTimeout());
        $this->assertSame(60, $rData->getTtl());
    }

    private function assertPacketEquals(string $expected, string $actual)
    {
        $this->assertEquals($expected, bin2hex($actual));
    }

    private function assertMessageHeaderEquals(int $id, int $opCode, int $responseCode, int $flags, int $qdCount, int $anCount, int $nsCount, int $arCount, Message $message)
    {
        $this->assertSame($id, $message->getId());
        $this->assertSame($opCode, $message->getOpCode());
        $this->assertSame($responseCode, $message->getResponseCode());
        $this->assertSame($flags, $message->getFlags());
        $this->assertCount($qdCount, $message->getQuestionRecords());
        $this->assertCount($anCount, $message->getAnswerRecords());
        $this->assertCount($nsCount, $message->getAuthorityRecords());
        $this->assertCount($arCount, $message->getAdditionalRecords());
    }

    private function assertQuestionRecordHeaderEquals(string $name, int $class, int $type, QuestionRecord $record)
    {
        $this->assertSame($name, (string)$record->getName());
        $this->assertSame($class, $record->getClass());
        $this->assertSame($type, $record->getType());
    }

    private function assertResourceRecordHeaderEquals(string $name, int $class, int $type, int $ttl, ResourceRecord $record)
    {
        $this->assertSame($name, (string)$record->getName());
        $this->assertSame($class, $record->getClass());
        $this->assertSame($type, $record->getType());
        $this->assertSame($ttl, $record->getTTL());
    }
}
