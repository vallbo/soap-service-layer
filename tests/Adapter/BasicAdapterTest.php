<?php

namespace Vallbo\Test\SoapServiceLayer\Adapter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Vallbo\SoapServiceLayer\Adapter\BasicAdapter;
use Vallbo\SoapServiceLayer\Adapter\SoapClientFactoryInterface;
use Vallbo\SoapServiceLayer\Adapter\SoapSettings;
use Vallbo\SoapServiceLayer\Exception\SoapException;
use Vallbo\SoapServiceLayer\Request\RequestInterface;
use Vallbo\SoapServiceLayer\Response\ResponseFactoryInterface;
use Vallbo\SoapServiceLayer\Response\SoapResponseInterface;

class BasicAdapterTest extends TestCase
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
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->soapClientFactory = $this->createMock(SoapClientFactoryInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->instance = new BasicAdapter(
            $this->soapClientFactory,
            $this->responseFactory,
            $this->logger,
            null,
            null
        );
    }

    public function testSuccess()
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
        /** @var MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var MockObject $response */
        $response = $this->createMock(SoapResponseInterface::class);
        /** @var MockObject $soapClientMock */
        $soapClientMock = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();

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

        $result = $this->instance->requestWithoutCache($soapSettingsMock, $request);
        $this->assertEquals($response, $result);
    }

    public function testFail()
    {
        $methodName = uniqid();
        $wsdl = uniqid() . ".wsdl";
        $requestData = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $exceptionCode = uniqid();
        $exceptionMessage = uniqid();
        $responseObject = new \SoapFault($exceptionCode, $exceptionMessage);

        /** @var MockObject $soapSettingsMock */
        $soapSettingsMock = $this->createMock(SoapSettings::class);
        /** @var MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var MockObject $soapClientMock */
        $soapClientMock = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();

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
        $this->logger->expects(self::once())->method('critical');

        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('');
        $this->expectException(SoapException::class);
        $this->instance->requestWithoutCache($soapSettingsMock, $request);
    }
}
