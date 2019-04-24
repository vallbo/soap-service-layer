<?php

namespace Vallbo\SoapServiceLayer\Adapter;

class SoapClientFactory implements SoapClientFactoryInterface
{

    /**
     * @var array|\SoapClient[]
     */
    private $clientPool = [];

    /**
     * @param string $wsdl
     * @param array $options
     * @return \SoapClient
     */
    public function getClient(string $wsdl, array $options): \SoapClient
    {
        $hash = sha1(serialize($options));
        if (!isset($this->clientPool[$hash])) {
            $soapClient = new \SoapClient(
                $wsdl,
                $options
            );
            $this->clientPool[$hash] = $soapClient;
        }
        return $this->clientPool[$hash];
    }
}
