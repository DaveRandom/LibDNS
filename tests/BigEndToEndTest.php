<?php

namespace LibDNS\Tests;

use LibDNS\Decoder\DecoderFactory;
use LibDNS\Encoder\EncoderFactory;
use LibDNS\Messages\Message;
use LibDNS\Messages\MessageFactory;
use LibDNS\Messages\MessageTypes;
use LibDNS\Records\QuestionFactory;
use LibDNS\Records\ResourceQTypes;
use LibDNS\Records\ResourceTypes;
use LibDNS\Records\TypeDefinitions\TypeDefinitionManagerFactory;
use PHPUnit\Framework\TestCase;

final class BigEndToEndTest extends TestCase
{
    public function testARecordQuestionCanBeEncodedIntoAPacket()
    {
        $queryName = 'google.com';

        $question = (new QuestionFactory)->create(ResourceQTypes::A);
        $question->setName($queryName);

        $request = (new MessageFactory)->create(MessageTypes::QUERY);
        $request->getQuestionRecords()->add($question);
        $request->isRecursionDesired(true);

        $encoder = (new EncoderFactory)->create();
        $requestPacket = $encoder->encode($request);

        $this->assertPacketEquals(
            '00000100000100000000000006676f6f676c6503636f6d0000010001',
            $requestPacket
        );
    }

    public function testARecordResponseCanBeDecoded()
    {
        $responsePacket = hex2bin('00008180000100010000000006676f6f676c6503636f6d0000010001c00c000100010000012b0004d83ace6e');

        $decoder = (new DecoderFactory)->create();
        $response = $decoder->decode($responsePacket);

        $this->assertResponseCodeEquals(0, $response);
        $this->assertAnswersEqual(['216.58.206.110'], $response);
    }

    public function testSoaRecordQuestionCanBeEncodedIntoAPacket()
    {
        $queryName = 'google.com';

        $question = (new QuestionFactory)->create(ResourceQTypes::SOA);
        $question->setName($queryName);

        $request = (new MessageFactory)->create(MessageTypes::QUERY);
        $request->getQuestionRecords()->add($question);
        $request->isRecursionDesired(true);

        $encoder = (new EncoderFactory)->create();
        $requestPacket = $encoder->encode($request);

        $this->assertPacketEquals(
            '00000100000100000000000006676f6f676c6503636f6d0000060001',
            $requestPacket
        );
    }

    public function testSoaRecordResponseCanBeDecoded()
    {
        $responsePacket = hex2bin('00008180000100010000000006676f6f676c6503636f6d0000060001c00c000600010000003b0026036e7331c00c09646e732d61646d696ec00c0accab9e0000038400000384000007080000003c');

        $typeDefs = (new TypeDefinitionManagerFactory)->create();
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

        $decoder = (new DecoderFactory)->create($typeDefs);
        $response = $decoder->decode($responsePacket);

        $this->assertResponseCodeEquals(0, $response);

        $answers = $response->getAnswerRecords();
        $this->assertCount(1, $answers);

        $expectedFirstAnswer =
'{
    Primary Name Server : ns1.google.com
    Responsible Mail    : dns-admin.google.com
    Serial              : 181185438
    Refresh             : 900
    Retry               : 900
    Expire              : 1800
    Default TTL         : 60
}';
        $this->assertAnswersEqual([$expectedFirstAnswer], $response);
    }

    private function assertPacketEquals(string $expected, string $actual)
    {
        $this->assertEquals($expected, bin2hex($actual));
    }

    private function assertResponseCodeEquals(int $expected, Message $response)
    {
        $this->assertEquals($expected, $response->getResponseCode());
    }

    private function assertAnswersEqual(array $expected, Message $response)
    {
        $answers = $response->getAnswerRecords();

        $this->assertCount(count($expected), $answers);

        foreach ($answers as $i => $answer) {
            $this->assertEquals($expected[$i], (string) $answer->getData());
        }
    }
}