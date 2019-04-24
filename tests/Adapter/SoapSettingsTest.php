<?php

namespace Vallbo\Test\SoapServiceLayer\Adapter;

use PHPUnit\Framework\TestCase;
use Vallbo\SoapServiceLayer\Adapter\SoapSettings;

class SoapSettingsTest extends TestCase
{

    public function testSettings()
    {
        $wsdl = uniqid();
        $location = uniqid();
        $soapVersion = SOAP_1_2;
        $cacheWsdl = WSDL_CACHE_BOTH;

        $instance = new SoapSettings($wsdl, $location, $soapVersion, $cacheWsdl);
        $this->assertTrue($instance->isTrace());
        $this->assertFalse($instance->isExceptions());
        $instance->setTrace(false);
        $instance->setExceptions(true);
        $this->assertEquals($wsdl, $instance->getWsdl());
        $this->assertEquals($location, $instance->getLocation());
        $this->assertEquals($soapVersion, $instance->getSoapVersion());
        $this->assertEquals($cacheWsdl, $instance->getCacheWsdl());
        $this->assertFalse($instance->isTrace());
        $this->assertTrue($instance->isExceptions());
        $this->assertEquals(
            [
                'location' => $location,
                'exceptions' => true,
                'cache_wsdl' => $cacheWsdl,
                'trace' => false,
                'soap_version' => $soapVersion,
            ],
            $instance->getOptions()
        );
    }
}
