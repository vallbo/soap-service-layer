<?php

namespace Vallbo\SoapServiceLayer\Adapter;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Vallbo\SoapServiceLayer\Exception\SoapException;
use Vallbo\SoapServiceLayer\Request\RequestInterface;
use Vallbo\SoapServiceLayer\Response\ResponseFactoryInterface;
use Vallbo\SoapServiceLayer\Response\SoapResponseInterface;

class BasicAdapter
{

    /**
     * @var SoapClientFactoryInterface
     */
    private $soapClientFactory;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @var int|null
     */
    private $cacheTTL;

    /**
     * BasicAdapter constructor.
     * @param SoapClientFactoryInterface $soapClientFactory
     * @param ResponseFactoryInterface $responseFactory
     * @param LoggerInterface $logger
     * @param CacheInterface|null $cache
     * @param int|null $cacheTTL
     */
    public function __construct(
        SoapClientFactoryInterface $soapClientFactory,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger,
        ?CacheInterface $cache,
        ?int $cacheTTL
    ) {
        $this->soapClientFactory = $soapClientFactory;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->cacheTTL = $cacheTTL;
    }

    /**
     * @param SoapSettings $soapSettings
     * @param RequestInterface $request
     * @return SoapResponseInterface
     * @throws SoapException
     */
    public function request(SoapSettings $soapSettings, RequestInterface $request): SoapResponseInterface
    {
        if ($this->cache === null || !$request->canCache()) {
            return $this->requestWithoutCache($soapSettings, $request);
        }

        $cacheKey = $this->getCacheKey($request);
        try {
            if ($this->cache->has($cacheKey)) {
                $response = $this->cache->get($cacheKey);
                $this->logger->info("GET FROM CACHE", [$request, $response]);
                return $response;
            }
            $response = $this->requestWithoutCache($soapSettings, $request);
            $this->cache->set($cacheKey, $response, $this->cacheTTL);
            return $response;
        } catch (CacheException $exception) {
            return $this->requestWithoutCache($soapSettings, $request);
        }
    }

    /**
     * @param SoapSettings $soapSettings
     * @param RequestInterface $request
     * @return SoapResponseInterface
     * @throws SoapException
     */
    public function requestWithoutCache(SoapSettings $soapSettings, RequestInterface $request): SoapResponseInterface
    {
        $responseHeaders = [];

        $options = $this->createOptions($soapSettings->getOptions(), $request);
        $soapClient = $this->soapClientFactory->getClient($soapSettings->getWsdl(), $options);
        $response = $soapClient->__soapCall(
            $request->getMethodName(),
            [$request->getRequestData()],
            [],
            $request->getSoapHeaders(),
            $responseHeaders
        );

        if (is_soap_fault($response)) {
            $this->logger->critical("WS EXCEPTION", [$request, $response, $soapClient]);
            throw new SoapException(
                $soapClient->__getLastResponse(),
                0,
                $response
            );
        }

        $jsonData = json_encode($response);
        $responseData = json_decode(($jsonData ?: ''), true);

        $this->logger->info("GET FROM WS", [$request, $response, $soapClient]);
        return $this->responseFactory->create($responseData, $responseHeaders);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getCacheKey(RequestInterface $request): string
    {
        $base = $request->getMethodName();
        $base .= serialize($request->getRequestData());
        $base .= serialize($request->getHttpHeaders());
        return sha1($base);
    }

    /**
     * @param array $clientOptions
     * @param RequestInterface $request
     * @return array
     */
    private function createOptions(array $clientOptions, RequestInterface $request): array
    {
        $httpHeaders = $request->getHttpHeaders();
        if (empty($httpHeaders)) {
            return $clientOptions;
        }
        $result = [];
        foreach ($httpHeaders as $name => $httpHeader) {
            $result[] = $name . ": " . implode(',', $httpHeader);
        }
        $context = [
            'http' => [
                'header' => implode("\r\n", $result),
            ],
        ];
        $clientOptions['stream_context'] = stream_context_create($context);
        return $clientOptions;
    }
}
