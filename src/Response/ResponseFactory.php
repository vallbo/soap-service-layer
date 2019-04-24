<?php

namespace Vallbo\SoapServiceLayer\Response;

class ResponseFactory implements ResponseFactoryInterface
{

    /**
     * @param array $responseData
     * @param array $responseHeaders
     * @return SoapResponseInterface
     */
    public function create(array $responseData, array $responseHeaders): SoapResponseInterface
    {
        return new SoapResponse(
            $responseData,
            $responseHeaders
        );
    }
}
