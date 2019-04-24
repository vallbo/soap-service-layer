<?php

namespace Vallbo\Test\SoapServiceLayer\Adapter;

use PHPUnit\Framework\TestCase;
use Vallbo\SoapServiceLayer\Adapter\SoapClientFactory;

class SoapClientFactoryTest extends TestCase
{

    /**
     * @var SoapClientFactory
     */
    private $instance;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->instance = new SoapClientFactory();
    }

    public function testGetClient()
    {
        $wsdl = __DIR__ . '/calculator.wsdl';
        $options = [];

        $client = $this->instance->getClient($wsdl, $options);
        $this->assertInstanceOf(\SoapClient::class, $client);
        $this->assertEquals(
            $client,
            $this->instance->getClient($wsdl, $options)
        );
    }
}
