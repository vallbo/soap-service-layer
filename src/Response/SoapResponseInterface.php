<?php

namespace Vallbo\SoapServiceLayer\Response;

interface SoapResponseInterface
{

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool;

    /**
     * @param string $name
     * @return array
     */
    public function getHeader(string $name): array;

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name);

    /**
     * @return array
     */
    public function getBody(): array;
}
