<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPAddress;

final class ParsingContext
{
    private $map = [];

    private $pendingData = '';

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

        $family = $address->getProtocolFamily();

        for ($i = 1; isset($parts[$i]); $i++) {
            try {
                $name = (string)DomainName::createFromString($parts[$i]);
            } catch (\InvalidArgumentException $e) {
                continue;
            }

            $this->map[$name][$family] = $address;
        }
    }

    public function addData(string $data)
    {
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
    }

    public function getResult(): HostsFile
    {
        if ($this->pendingData !== '') {
            $this->parseLine($this->pendingData);
            $this->pendingData = '';
        }

        return new HostsFile($this->map);
    }
}
