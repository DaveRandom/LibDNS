<?php

namespace DaveRandom\LibDNS\Tests\HostsFile;

use DaveRandom\LibDNS\HostsFile\HostsFile;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPv4Address;
use DaveRandom\Network\IPv6Address;
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
            self::BOTH_RECORD['name'] => [
                \STREAM_PF_INET  => IPv4Address::parse(self::BOTH_RECORD['v4']),
                \STREAM_PF_INET6 => IPv6Address::parse(self::BOTH_RECORD['v6']),
            ],
            self::IPV4_ONLY_RECORD['name'] => [
                \STREAM_PF_INET  => IPv4Address::parse(self::IPV4_ONLY_RECORD['v4']),
            ],
            self::IPV6_ONLY_RECORD['name'] => [
                \STREAM_PF_INET6 => IPv6Address::parse(self::IPV6_ONLY_RECORD['v6']),
            ],
        ];
    }

    public function testContainsNameWithNonExistentName()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertFalse($hostsFile->containsName(self::NON_EXISTENT_NAME));
        $this->assertFalse($hostsFile->containsName(self::NON_EXISTENT_NAME, \STREAM_PF_INET));
        $this->assertFalse($hostsFile->containsName(self::NON_EXISTENT_NAME, \STREAM_PF_INET6));

        $name = DomainName::createFromString(self::NON_EXISTENT_NAME);
        $this->assertFalse($hostsFile->containsName($name));
        $this->assertFalse($hostsFile->containsName($name, \STREAM_PF_INET));
        $this->assertFalse($hostsFile->containsName($name, \STREAM_PF_INET6));
    }

    public function testContainsNameWithInvalidName()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertFalse($hostsFile->containsName(self::INVALID_NAME));
        $this->assertFalse($hostsFile->containsName(self::INVALID_NAME, \STREAM_PF_INET));
        $this->assertFalse($hostsFile->containsName(self::INVALID_NAME, \STREAM_PF_INET6));
    }

    public function testContainsNameWithIPv4Only()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertTrue($hostsFile->containsName(self::IPV4_ONLY_RECORD['name']));
        $this->assertTrue($hostsFile->containsName(self::IPV4_ONLY_RECORD['name'], \STREAM_PF_INET));
        $this->assertFalse($hostsFile->containsName(self::IPV4_ONLY_RECORD['name'], \STREAM_PF_INET6));

        $name = DomainName::createFromString(self::IPV4_ONLY_RECORD['name']);
        $this->assertTrue($hostsFile->containsName($name));
        $this->assertTrue($hostsFile->containsName($name, \STREAM_PF_INET));
        $this->assertFalse($hostsFile->containsName($name, \STREAM_PF_INET6));
    }

    public function testContainsNameWithIPv6Only()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertFalse($hostsFile->containsName(self::IPV6_ONLY_RECORD['name']));
        $this->assertFalse($hostsFile->containsName(self::IPV6_ONLY_RECORD['name'], \STREAM_PF_INET));
        $this->assertTrue($hostsFile->containsName(self::IPV6_ONLY_RECORD['name'], \STREAM_PF_INET6));

        $name = DomainName::createFromString(self::IPV6_ONLY_RECORD['name']);
        $this->assertFalse($hostsFile->containsName($name));
        $this->assertFalse($hostsFile->containsName($name, \STREAM_PF_INET));
        $this->assertTrue($hostsFile->containsName($name, \STREAM_PF_INET6));
    }

    public function testContainsNameWithBoth()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertTrue($hostsFile->containsName(self::BOTH_RECORD['name']));
        $this->assertTrue($hostsFile->containsName(self::BOTH_RECORD['name'], \STREAM_PF_INET));
        $this->assertTrue($hostsFile->containsName(self::BOTH_RECORD['name'], \STREAM_PF_INET6));

        $name = DomainName::createFromString(self::BOTH_RECORD['name']);
        $this->assertTrue($hostsFile->containsName($name));
        $this->assertTrue($hostsFile->containsName($name, \STREAM_PF_INET));
        $this->assertTrue($hostsFile->containsName($name, \STREAM_PF_INET6));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithNonExistentNameDefaultFamily()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::NON_EXISTENT_NAME);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithNonExistentNameIPv4()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::NON_EXISTENT_NAME, \STREAM_PF_INET);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithNonExistentNameIPv6()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::NON_EXISTENT_NAME, \STREAM_PF_INET6);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithInvalidNameDefaultFamily()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::INVALID_NAME);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithInvalidNameIPv4()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::INVALID_NAME, \STREAM_PF_INET);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithInvalidNameIPv6()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::INVALID_NAME, \STREAM_PF_INET6);
    }

    public function testGetAddressForNameWithExistingIPv4()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertSame(self::IPV4_ONLY_RECORD['v4'], (string)$hostsFile->getAddressForName(self::IPV4_ONLY_RECORD['name']));
        $this->assertSame(self::IPV4_ONLY_RECORD['v4'], (string)$hostsFile->getAddressForName(self::IPV4_ONLY_RECORD['name'], \STREAM_PF_INET));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithNonExistentIPv4DefaultFamily()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::IPV6_ONLY_RECORD['name']);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithNonExistentIPv4ExplicitFamily()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::IPV6_ONLY_RECORD['name'], \STREAM_PF_INET);
    }

    public function testGetAddressForNameWithExistingIPv6()
    {
        $hostsFile = new HostsFile($this->provideData());

        $this->assertSame(self::IPV6_ONLY_RECORD['v6'], (string)$hostsFile->getAddressForName(self::IPV6_ONLY_RECORD['name'], \STREAM_PF_INET6));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAddressForNameWithNonExistentIPv6()
    {
        (new HostsFile($this->provideData()))->getAddressForName(self::IPV4_ONLY_RECORD['name'], \STREAM_PF_INET6);
    }

    public function testToArray()
    {
        $data = $this->provideData();

        $hostsFile = new HostsFile($data);

        $this->assertSame($data, $hostsFile->toArray());
    }
}
