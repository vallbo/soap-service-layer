<?php

namespace Vallbo\SoapServiceLayer\Adapter;

class SoapSettings
{

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var string
     */
    private $location;

    /**
     * @var int
     */
    private $soapVersion;

    /**
     * @var int
     */
    private $cacheWsdl;

    /**
     * @var bool
     */
    private $trace = true;

    /**
     * @var bool
     */
    private $exceptions = false;

    /**
     * SoapSettings constructor.
     * @param string $wsdl
     * @param string $location
     * @param int $soapVersion
     * @param int $cacheWsdl
     */
    public function __construct(string $wsdl, string $location, int $soapVersion, int $cacheWsdl)
    {
        $this->wsdl = $wsdl;
        $this->location = $location;
        $this->soapVersion = $soapVersion;
        $this->cacheWsdl = $cacheWsdl;
    }

    /**
     * @param bool $trace
     */
    public function setTrace(bool $trace): void
    {
        $this->trace = $trace;
    }

    /**
     * @param bool $exceptions
     */
    public function setExceptions(bool $exceptions): void
    {
        $this->exceptions = $exceptions;
    }

    /**
     * @return string
     */
    public function getWsdl(): string
    {
        return $this->wsdl;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getSoapVersion(): int
    {
        return $this->soapVersion;
    }

    /**
     * @return int
     */
    public function getCacheWsdl(): int
    {
        return $this->cacheWsdl;
    }

    /**
     * @return bool
     */
    public function isTrace(): bool
    {
        return $this->trace;
    }

    /**
     * @return bool
     */
    public function isExceptions(): bool
    {
        return $this->exceptions;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'location' => $this->location,
            'exceptions' => $this->exceptions,
            'cache_wsdl' => $this->cacheWsdl,
            'trace' => $this->trace,
            'soap_version' => $this->soapVersion,
        ];
    }
}
