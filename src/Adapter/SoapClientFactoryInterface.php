<?php

namespace Vallbo\SoapServiceLayer\Adapter;

interface SoapClientFactoryInterface
{

    /**
     * @param string $wsdl
     * @param array $options
     * @return \SoapClient
     */
    public function getClient(string $wsdl, array $options): \SoapClient;
}
