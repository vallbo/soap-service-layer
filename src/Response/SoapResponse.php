<?php

namespace Vallbo\SoapServiceLayer\Response;

class SoapResponse implements SoapResponseInterface
{

    /**
     * @var array
     */
    private $body = [];

    /**
     * @var array
     */
    private $headers = [];

    /**
     * SoapResponse constructor.
     * @param array $body
     * @param array $headers
     */
    public function __construct(array $body, array $headers)
    {
        $this->body = $body;
        foreach ($headers as $key => $value) {
            $this->headers[$key] = (is_array($value) ? $value : [$value]);
        }
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getHeader(string $name): array
    {
        return ($this->headers[$name] ?? []);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name)
    {
        return (isset($this->headers[$name]) ? implode(',', $this->headers[$name]) : '');
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
