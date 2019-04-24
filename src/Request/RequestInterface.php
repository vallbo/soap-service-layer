<?php

namespace Vallbo\SoapServiceLayer\Request;

interface RequestInterface
{

    /**
     * @return string
     */
    public function getMethodName(): string;

    /**
     * @return array
     */
    public function getRequestData(): array;

    /**
     * @return array
     */
    public function getHttpHeaders(): array;

    /**
     * @return array|\SoapHeader[]
     */
    public function getSoapHeaders(): array;

    /**
     * @return bool
     */
    public function canCache(): bool;
}
