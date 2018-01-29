<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

use DaveRandom\LibDNS\Records\ResourceTypes;
use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPAddress;

final class HostsFile
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param string|DomainName $name
     */
    public function containsName($name, int $type = null): bool
    {
        if (!$name instanceof DomainName) {
            try {
                $name = DomainName::createFromString((string)$name);
            } catch (\InvalidArgumentException $e) {
                return false;
            }
        }

        $name = (string)$name;

        return isset($type)
            ? isset($this->map[$type][$name])
            : isset($this->map[ResourceTypes::A][$name]) || isset($this->map[ResourceTypes::AAAA][$name]);
    }

    /**
     * @return IPAddress|null
     */
    public function getAddressForName($name, int $type = null)
    {
        if (!$name instanceof DomainName) {
            try {
                $name = (string)DomainName::createFromString((string)$name);
            } catch (\InvalidArgumentException $e) {
                $name = null;
            }
        }

        $name = (string)$name;

        if (isset($type)) {
            return $this->map[$type][$name] ?? null;
        }

        return $this->map[ResourceTypes::A][$name] ?? $this->map[ResourceTypes::AAAA][$name] ?? null;
    }

    public function toArray(): array
    {
        return $this->map;
    }
}
