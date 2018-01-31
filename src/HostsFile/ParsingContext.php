<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPAddress;
use DaveRandom\Network\IPv4Address;
use DaveRandom\Network\IPv6Address;

final class ParsingContext
{
    private $map = [];
    private $hostsFile = null;
    private $pendingData = '';

    private $flags;

    private function parseLine(string $line)
    {
        $parts = \preg_split('/\s+/', $line, -1, \PREG_SPLIT_NO_EMPTY);

        if (\count($parts) === 0 || $parts[0][0] === '#') {
            return;
        }

        try {
            $address = IPAddress::parse($parts[0]);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $type = $address->getProtocolFamily() === STREAM_PF_INET ? ResourceTypes::A : ResourceTypes::AAAA;

        for ($i = 1; isset($parts[$i]); $i++) {
            try {
                $name = (string)DomainName::createFromString($parts[$i]);
            } catch (\InvalidArgumentException $e) {
                continue;
            }

            $this->map[$type][$name] = $address;
        }
    }

    private static function getSystemLocalhostEntries(): array
    {
        static $entries = null;

        if ($entries !== null) {
            return $entries;
        }

        $entries = [];

        if (\PHP_OS !== 'WINNT') {
            return $entries;
        }

        /* On Windows (since Vista) the handling of localhost is built in to the system resolver. The hosts file no
           longer contains an entry for localhost, and if an entry is created then it is ignored.
           See https://serverfault.com/a/9665/92780

           I have been unable to find a way to modify the address mapped to localhost - although I suspect there is an
           undocumented registry setting - so for now, these values are hard-coded. This code uses checkdnsrr() to
           determine whether the system resolver returns A/AAAA records for localhost, because we want to emulate the
           system resolver's behaviour exactly.

           checkdnsrr() is an instantaneous call in this case, the system resolver will never query a remote server for
           an unqualified lookup of localhost, so this code shouldn't make a meaningful difference to performance. */
        if (\checkdnsrr('localhost', 'A')) {
            $entries[ResourceTypes::A] = ['localhost' => new IPv4Address(127, 0, 0, 1)];
        }

        if (\checkdnsrr('localhost', 'AAAA')) {
            $entries[ResourceTypes::AAAA] = ['localhost' => new IPv6Address(0, 0, 0, 0, 0, 0, 0, 1)];
        }

        return $entries;
    }

    public function __construct(int $flags = Parser::USE_SYSTEM_LOCALHOST_BEHAVIOUR)
    {
        $this->flags = $flags;
    }

    public function addData(string $data): self
    {
        if ($this->hostsFile !== null) {
            throw new \LogicException('Parsing context already finalized');
        }

        $data = $this->pendingData . $data;
        $length = \strlen($data);
        $pos = 0;

        while ($pos < $length) {
            if (false === $eolPos = \strpos($data, "\n", $pos)) {
                $this->pendingData = \substr($data, $pos);
                break;
            }

            $lineLength = ($eolPos - $pos) + 1;

            $this->parseLine(\substr($data, $pos, $lineLength));
            $pos += $lineLength;
        }

        return $this;
    }

    public function getResult(): HostsFile
    {
        if ($this->hostsFile !== null) {
            return $this->hostsFile;
        }

        if ($this->pendingData !== '') {
            $this->parseLine($this->pendingData);
            $this->pendingData = '';
        }

        // Overwrite loaded entries with hard-coded ones
        if ($this->flags & Parser::USE_SYSTEM_LOCALHOST_BEHAVIOUR) {
            foreach (self::getSystemLocalhostEntries() as $type => $entries) {
                foreach ($entries as $name => $address) {
                    $this->map[$type][$name] = $address;
                }
            }
        }

        return $this->hostsFile = new HostsFile($this->map);
    }
}
