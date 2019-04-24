<?php

namespace Vallbo\Test\SoapServiceLayer\Adapter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Vallbo\SoapServiceLayer\Adapter\BasicAdapter;
use Vallbo\SoapServiceLayer\Adapter\SoapClientFactoryInterface;
use Vallbo\SoapServiceLayer\Adapter\SoapSettings;
use Vallbo\SoapServiceLayer\Exception\SoapException;
use Vallbo\SoapServiceLayer\Request\RequestInterface;
use Vallbo\SoapServiceLayer\Response\ResponseFactoryInterface;
use Vallbo\SoapServiceLayer\Response\SoapResponseInterface;

class BasicAdapterWithCacheTest extends TestCase
{

    /**
     * @var SoapClientFactoryInterface|MockObject
     */
    private $soapClientFactory;

    /**
     * @var ResponseFactoryInterface|MockObject
     */
    private $responseFactory;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var BasicAdapter
     */
    private $instance;

    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->soapClientFactory = $this->createMock(SoapClientFactoryInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->instance = new BasicAdapter(
            $this->soapClientFactory,
            $this->responseFactory,
            $this->logger,
            $this->cache,
            null
        );
    }

    public function testFromCache()
    {
        $methodName = uniqid();
        $requestData = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $requestHeaders = [
            uniqid() => uniqid(),
        ];
        $aaa = uniqid();
        $bbb = uniqid();
        $responseObject = new \stdClass();
        $responseObject->aaa = $aaa;
        $responseObject->bbb = $bbb;

        $base = $methodName . serialize($requestData) . serialize($requestHeaders);
        $cacheKey = sha1($base);

        /** @var MockObject $soapSettingsMock */
        $soapSettingsMock = $this->createMock(SoapSettings::class);
        /** @var MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var MockObject $response */
        $response = $this->createMock(SoapResponseInterface::class);

        $request->expects(self::once())
            ->method('canCache')
            ->willReturn(true);
        $request->expects(self::once())
            ->method('getMethodName')
            ->willReturn($methodName);
        $request->expects(self::once())
            ->method('getRequestData')
            ->willReturn($requestData);
        $request->expects(self::once())
            ->method('getHttpHeaders')
            ->willReturn($requestHeaders);
        $this->cache->expects(self::once())
            ->method('has')
            ->with($cacheKey)
            ->willReturn(true);
        $this->cache->expects(self::once())
            ->method('get')
            ->with($cacheKey)
            ->willReturn($response);
        $this->logger->expects(self::once())->method('info');

        $result = $this->instance->request($soapSettingsMock, $request);
        $this->assertEquals($response, $result);
    }

    public function testLoadToCache()
    {
        $wsdl = uniqid() . ".wsdl";
        $methodName = uniqid();
        $requestData = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $requestHeaders = [
            uniqid() => [uniqid()],
        ];
        $aaa = uniqid();
        $bbb = uniqid();
        $responseObject = new \stdClass();
        $responseObject->aaa = $aaa;
        $responseObject->bbb = $bbb;

        $base = $methodName . serialize($requestData) . serialize($requestHeaders);
        $cacheKey = sha1($base);

        /** @var MockObject $soapSettingsMock */
        $soapSettingsMock = $this->createMock(SoapSettings::class);
        /** @var MockObject $soapClientMock */
        $soapClientMock = $this->createMock(\SoapClient::class);
        /** @var MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var MockObject $response */
        $response = $this->createMock(SoapResponseInterface::class);

        $request->expects(self::once())
            ->method('canCache')
            ->willReturn(true);
        $request->expects(self::exactly(2))
            ->method('getMethodName')
            ->willReturn($methodName);
        $request->expects(self::exactly(2))
            ->method('getRequestData')
            ->willReturn($requestData);
        $request->expects(self::exactly(2))
            ->method('getHttpHeaders')
            ->willReturn($requestHeaders);
        $this->cache->expects(self::once())
            ->method('has')
            ->with($cacheKey)
            ->willReturn(false);
        $soapSettingsMock->expects(self::once())
            ->method('getOptions')
            ->willReturn([]);
        $soapSettingsMock->expects(self::once())
            ->method('getWsdl')
            ->willReturn($wsdl);
        $this->soapClientFactory->expects(self::once())
            ->method('getClient')
            ->with($wsdl)
            ->willReturn($soapClientMock);
        $soapClientMock->expects(self::once())
            ->method('__soapCall')
            ->with(
                $methodName,
                [$requestData],
                [],
                [],
                []
            )
            ->willReturn($responseObject);
        $this->logger->expects(self::once())->method('info');
        $this->responseFactory->expects(self::once())
            ->method('create')
            ->with(
                [
                    'aaa' => $aaa,
                    'bbb' => $bbb,
                ],
                []
            )
            ->willReturn(
                $response
            );
        $this->cache->expects(self::once())
            ->method('set')
            ->with($cacheKey, $response, null);


        $result = $this->instance->request($soapSettingsMock, $request);
        $this->assertEquals($response, $result);
    }

    public function testCacheException()
    {
        $wsdl = uniqid() . ".wsdl";
        $methodName = uniqid();
        $requestData = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $requestHeaders = [
            uniqid() => [uniqid()],
        ];
        $aaa = uniqid();
        $bbb = uniqid();
        $responseObject = new \stdClass();
        $responseObject->aaa = $aaa;
        $responseObject->bbb = $bbb;

        $base = $methodName . serialize($requestData) . serialize($requestHeaders);
        $cacheKey = sha1($base);

        /** @var MockObject $soapSettingsMock */
        $soapSettingsMock = $this->createMock(SoapSettings::class);
        /** @var MockObject $soapClientMock */
        $soapClientMock = $this->createMock(\SoapClient::class);
        /** @var MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var MockObject $response */
        $response = $this->createMock(SoapResponseInterface::class);

        $request->expects(self::once())
            ->method('canCache')
            ->willReturn(true);
        $request->expects(self::exactly(2))
            ->method('getMethodName')
            ->willReturn($methodName);
        $request->expects(self::exactly(2))
            ->method('getRequestData')
            ->willReturn($requestData);
        $request->expects(self::exactly(2))
            ->method('getHttpHeaders')
            ->willReturn($requestHeaders);
        $this->cache->expects(self::once())
            ->method('has')
            ->with($cacheKey)
            ->willThrowException(
                new class extends \Exception implements CacheException
                {

                }
            );
        $soapSettingsMock->expects(self::once())
            ->method('getOptions')
            ->willReturn([]);
        $soapSettingsMock->expects(self::once())
            ->method('getWsdl')
            ->willReturn($wsdl);
        $this->soapClientFactory->expects(self::once())
            ->method('getClient')
            ->with($wsdl)
            ->willReturn($soapClientMock);
        $soapClientMock->expects(self::once())
            ->method('__soapCall')
            ->with(
                $methodName,
                [$requestData],
                [],
                [],
                []
            )
            ->willReturn($responseObject);
        $this->logger->expects(self::once())->method('info');
        $this->responseFactory->expects(self::once())
            ->method('create')
            ->with(
                [
                    'aaa' => $aaa,
                    'bbb' => $bbb,
                ],
                []
            )
            ->willReturn(
                $response
            );

        $result = $this->instance->request($soapSettingsMock, $request);
        $this->assertEquals($response, $result);
    }

    public function testCannotCache()
    {
        $methodName = uniqid();
        $wsdl = uniqid() . ".wsdl";
        $requestData = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $aaa = uniqid();
        $bbb = uniqid();
        $responseObject = new \stdClass();
        $responseObject->aaa = $aaa;
        $responseObject->bbb = $bbb;

        /** @var MockObject $soapSettingsMock */
        $soapSettingsMock = $this->createMock(SoapSettings::class);
        /** @var MockObject $soapClientMock */
        $soapClientMock = $this->createMock(\SoapClient::class);
        /** @var MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var MockObject $response */
        $response = $this->createMock(SoapResponseInterface::class);

        $request->expects(self::once())
            ->method('canCache')
            ->willReturn(false);
        $soapSettingsMock->expects(self::once())
            ->method('getOptions')
            ->willReturn([]);
        $soapSettingsMock->expects(self::once())
            ->method('getWsdl')
            ->willReturn($wsdl);
        $this->soapClientFactory->expects(self::once())
            ->method('getClient')
            ->with($wsdl, [])
            ->willReturn($soapClientMock);

        $request->expects(self::once())
            ->method('getMethodName')
            ->willReturn($methodName);
        $request->expects(self::once())
            ->method('getRequestData')
            ->willReturn($requestData);
        $request->expects(self::once())
            ->method('getSoapHeaders')
            ->willReturn([]);
        $soapClientMock->expects(self::once())
            ->method('__soapCall')
            ->with(
                $methodName,
                [$requestData],
                [],
                [],
                []
            )
            ->willReturn($responseObject);
        $this->logger->expects(self::once())->method('info');
        $this->responseFactory->expects(self::once())
            ->method('create')
            ->with(
                [
                    'aaa' => $aaa,
                    'bbb' => $bbb,
                ],
                []
            )
            ->willReturn(
                $response
            );

        $result = $this->instance->request($soapSettingsMock, $request);
        $this->assertEquals($response, $result);
    }
}
