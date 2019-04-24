<?php

namespace Vallbo\Test\SoapServiceLayer\Response;

use PHPUnit\Framework\TestCase;
use Vallbo\SoapServiceLayer\Response\ResponseFactory;
use Vallbo\SoapServiceLayer\Response\SoapResponse;

class BaseResponseTest extends TestCase
{

    /**
     * @var ResponseFactory
     */
    private $instance;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->instance = new ResponseFactory();
    }

    /**
     * @dataProvider getData
     * @param array $data
     * @param array $headers
     */
    public function testCreate(array $data, array $headers)
    {
        $result = $this->instance->create($data, $headers);

        foreach ($headers as $key => $value) {
            $headers[$key] = (is_array($value) ? $value : [$value]);
        }

        $this->assertInstanceOf(SoapResponse::class, $result);
        $this->assertEquals($data, $result->getBody());
        $this->assertEquals($headers, $result->getHeaders());
        if (!empty($headers)) {
            $keys = array_keys($headers);
            $key = current($keys);
            $value = $headers[$key];
            $this->assertTrue(
                $result->hasHeader($key)
            );
            $this->assertEquals($value, $result->getHeader($key));
            $this->assertEquals(implode(',', $value), $result->getHeaderLine($key));
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            [
                [
                    uniqid() => uniqid(),
                ],
                [],
            ],
            [
                [
                    uniqid() => uniqid(),
                ],
                [
                    uniqid() => [uniqid()],
                ],
            ],
            [
                [
                    uniqid() => uniqid(),
                ],
                [
                    uniqid() => uniqid(),
                ],
            ],
        ];
    }
}
