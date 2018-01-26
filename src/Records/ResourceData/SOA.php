<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class SOA implements ResourceData
{
    const TYPE_ID = 6;

    private $masterServerName;
    private $responsibleMailAddress;
    private $serial;
    private $refreshInterval;
    private $retryInterval;
    private $expireTimeout;
    private $ttl;

    public function __construct(
        DomainName $masterServerName,
        DomainName $responsibleMailAddress,
        int $serial,
        int $refreshInterval,
        int $retryInterval,
        int $expireTimeout,
        int $ttl,
        bool $extendedValidation = true
    ) {
        $this->masterServerName = $masterServerName;
        $this->responsibleMailAddress = $responsibleMailAddress;
        $this->serial = \DaveRandom\LibDNS\validate_uint32('Serial number', $serial);
        $this->refreshInterval = \DaveRandom\LibDNS\validate_uint32('Refresh interval', $refreshInterval);
        $this->retryInterval = \DaveRandom\LibDNS\validate_uint32('Retry interval', $retryInterval);
        $this->expireTimeout = \DaveRandom\LibDNS\validate_uint32('Expire timeout', $expireTimeout);
        $this->ttl = \DaveRandom\LibDNS\validate_uint32('Time-to-live', $ttl);

        if (!$extendedValidation) {
            return;
        }

        // These rules are specified in RFC 1912 sec 2.2

        if ($this->retryInterval >= $this->refreshInterval) {
            throw new \InvalidArgumentException('Retry interval must be less than refresh interval');
        }

        if ($this->expireTimeout <= $this->refreshInterval + $this->retryInterval) {
            throw new \InvalidArgumentException(
                'Expire timeout must be more than the sum of refresh interval and retry interval'
            );
        }
    }

    /**
     * @return DomainName
     */
    public function getMasterServerName(): DomainName
    {
        return $this->masterServerName;
    }

    public function getResponsibleMailAddress(): DomainName
    {
        return $this->responsibleMailAddress;
    }

    public function getSerial(): int
    {
        return $this->serial;
    }

    public function getRefreshInterval(): int
    {
        return $this->refreshInterval;
    }

    public function getRetryInterval(): int
    {
        return $this->retryInterval;
    }

    public function getExpireTimeout(): int
    {
        return $this->expireTimeout;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
