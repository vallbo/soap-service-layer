<?php

namespace Vallbo\SoapServiceLayer\Request;

abstract class BaseRequest implements RequestInterface
{

    /**
     * @var array
     */
    protected $httpHeaders = [];

    /**
     * @var array
     */
    protected $soapHeaders = [];

    /**
     * @return array
     */
    public function getHttpHeaders(): array
    {
        return $this->httpHeaders;
    }

    /**
     * @param array $httpHeaders
     */
    public function setHttpHeaders(array $httpHeaders): void
    {
        $this->httpHeaders = $httpHeaders;
    }

    /**
     * @param string $name
     * @param array $value
     */
    public function addHttpHeader(string $name, array $value): void
    {
        if (!isset($this->httpHeaders[$name])) {
            $this->httpHeaders[$name] = $value;
            return;
        }
        $this->httpHeaders[$name] = array_merge($this->httpHeaders[$name], $value);
    }

    /**
     * @return array
     */
    public function getSoapHeaders(): array
    {
        return $this->soapHeaders;
    }

    /**
     * @param array $soapHeaders
     */
    public function setSoapHeaders(array $soapHeaders): void
    {
        $this->soapHeaders = $soapHeaders;
    }

    /**
     * @param \SoapHeader $soapHeader
     */
    public function addSoapHeader(\SoapHeader $soapHeader): void
    {
        $this->soapHeaders[] = $soapHeader;
    }
}
