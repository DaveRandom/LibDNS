<?php

namespace DaveRandom\LibDNS\Tests\HostsFile;

use DaveRandom\LibDNS\HostsFile\HostsFile;
use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPv4Address;
use PHPUnit\Framework\TestCase;

final class HostsFileTest extends TestCase
{
    const NON_EXISTENT_NAME = 'not-there.com';
    const INVALID_NAME = "\x00invalid";
    const IPV4_ONLY_RECORD = ['name' => 'daverandom.com', 'v4' => '68.97.118.101'];
    const IPV6_ONLY_RECORD = ['name' => 'room11.org', 'v6' => '11::11'];
    const BOTH_RECORD = ['name' => 'google.com', 'v4' => '127.0.0.1', 'v6' => '::1'];

    private function provideData(): array
    {
        return [
            ResourceTypes::A => [
                self::BOTH_RECORD['name'] => IPv4Address::parse(self::BOTH_RECORD['v4']),
                self::IPV4_ONLY_RECORD['name'] => IPv4Address::parse(self::IPV4_ONLY_RECORD['v4']),
            ],
            ResourceTypes::AAAA => [
                self::BOTH_RECORD['name'] => IPv4Address::parse(self::BOTH_RECORD['v6']),
                self::IPV6_ONLY_RECORD['name'] => IPv4Address::parse(self::IPV6_ONLY_RECORD['v6']),
            ],
        ];
    }

    public function testContainsNameWithNonExistentName()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertFalse($hostsFile->containsName(self::NON_EXISTENT_NAME));
        $this->assertFalse($hostsFile->containsName(self::NON_EXISTENT_NAME, ResourceTypes::A));
        $this->assertFalse($hostsFile->containsName(self::NON_EXISTENT_NAME, ResourceTypes::AAAA));

        $name = DomainName::createFromString(self::NON_EXISTENT_NAME);
        $this->assertFalse($hostsFile->containsName($name));
        $this->assertFalse($hostsFile->containsName($name, ResourceTypes::A));
        $this->assertFalse($hostsFile->containsName($name, ResourceTypes::AAAA));
    }

    public function testContainsNameWithInvalidName()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertFalse($hostsFile->containsName(self::INVALID_NAME));
        $this->assertFalse($hostsFile->containsName(self::INVALID_NAME, ResourceTypes::A));
        $this->assertFalse($hostsFile->containsName(self::INVALID_NAME, ResourceTypes::AAAA));
    }

    public function testContainsNameWithIPv4Only()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertTrue($hostsFile->containsName(self::IPV4_ONLY_RECORD['name']));
        $this->assertTrue($hostsFile->containsName(self::IPV4_ONLY_RECORD['name'], ResourceTypes::A));
        $this->assertFalse($hostsFile->containsName(self::IPV4_ONLY_RECORD['name'], ResourceTypes::AAAA));

        $name = DomainName::createFromString(self::IPV4_ONLY_RECORD['name']);
        $this->assertTrue($hostsFile->containsName($name));
        $this->assertTrue($hostsFile->containsName($name, ResourceTypes::A));
        $this->assertFalse($hostsFile->containsName($name, ResourceTypes::AAAA));
    }

    public function testContainsNameWithIPv6Only()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertTrue($hostsFile->containsName(self::IPV6_ONLY_RECORD['name']));
        $this->assertFalse($hostsFile->containsName(self::IPV6_ONLY_RECORD['name'], ResourceTypes::A));
        $this->assertTrue($hostsFile->containsName(self::IPV6_ONLY_RECORD['name'], ResourceTypes::AAAA));

        $name = DomainName::createFromString(self::IPV6_ONLY_RECORD['name']);
        $this->assertTrue($hostsFile->containsName($name));
        $this->assertFalse($hostsFile->containsName($name, ResourceTypes::A));
        $this->assertTrue($hostsFile->containsName($name, ResourceTypes::AAAA));
    }

    public function testContainsNameWithBoth()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertTrue($hostsFile->containsName(self::BOTH_RECORD['name']));
        $this->assertTrue($hostsFile->containsName(self::BOTH_RECORD['name'], ResourceTypes::A));
        $this->assertTrue($hostsFile->containsName(self::BOTH_RECORD['name'], ResourceTypes::AAAA));

        $name = DomainName::createFromString(self::BOTH_RECORD['name']);
        $this->assertTrue($hostsFile->containsName($name));
        $this->assertTrue($hostsFile->containsName($name, ResourceTypes::A));
        $this->assertTrue($hostsFile->containsName($name, ResourceTypes::AAAA));
    }

    public function testGetAddressForNameWithNonExistentNameDefaultFamily()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::NON_EXISTENT_NAME));
    }

    public function testGetAddressForNameWithNonExistentNameIPv4()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::NON_EXISTENT_NAME, ResourceTypes::A));
    }

    public function testGetAddressForNameWithNonExistentNameIPv6()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::NON_EXISTENT_NAME, ResourceTypes::AAAA));
    }

    public function testGetAddressForNameWithInvalidNameDefaultFamily()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::INVALID_NAME));
    }

    public function testGetAddressForNameWithInvalidNameIPv4()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::INVALID_NAME, ResourceTypes::A));
    }

    public function testGetAddressForNameWithInvalidNameIPv6()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::INVALID_NAME, ResourceTypes::AAAA));
    }

    public function testGetAddressForNameWithExistingIPv4()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertSame(self::IPV4_ONLY_RECORD['v4'], (string)$hostsFile->getAddressForName(self::IPV4_ONLY_RECORD['name']));
        $this->assertSame(self::IPV4_ONLY_RECORD['v4'], (string)$hostsFile->getAddressForName(self::IPV4_ONLY_RECORD['name'], ResourceTypes::A));
    }

    public function testGetAddressForNameWithNonExistentIPv4()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::IPV6_ONLY_RECORD['name'], ResourceTypes::A));
    }

    public function testGetAddressForNameWithExistingIPv6()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertSame(self::IPV6_ONLY_RECORD['v6'], (string)$hostsFile->getAddressForName(self::IPV6_ONLY_RECORD['name'], ResourceTypes::AAAA));
    }

    public function testGetAddressForNameWithNonExistentIPv6()
    {
        $this->assertNull((new HostsFile($this->provideData()))->getAddressForName(self::IPV4_ONLY_RECORD['name'], ResourceTypes::AAAA));
    }

    public function testToArray()
    {
        $data = $this->provideData();

        $hostsFile = new HostsFile($data);

        $this->assertSame($data, $hostsFile->toArray());
    }
}
