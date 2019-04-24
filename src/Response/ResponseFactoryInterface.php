<?php

namespace Vallbo\SoapServiceLayer\Response;

interface ResponseFactoryInterface
{

    /**
     * @param array $responseData
     * @param array $responseHeaders
     * @return SoapResponseInterface
     */
    public function create(array $responseData, array $responseHeaders): SoapResponseInterface;
}
