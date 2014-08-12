<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Service\PaymentService;

class PaymentServiceTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->debug = true;
        $this->baseUrl = 'https://api.sandbox.paypal.com';
        $this->returnUrl = 'http://example.com/success';
        $this->cancelUrl = 'http://example.com/cancel';
        $this->total = "15.00";
        $this->currency = 'EUR';
        $this->description = 'My fantastic transaction description';

        $this->accessToken = $this->getMockBuilder('PayPalRestApiClient\Model\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();

        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $this->builder = $this->getMockBuilder('PayPalRestApiClient\Builder\PaymentRequestBodyBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock();

        $this->service = new PaymentService(
            $this->client,
            $this->builder,
            $this->baseUrl,
            $this->debug
        );
    }

    protected function initAccessToken($type, $token)
    {
        $this->accessToken->expects($this->atLeastOnce())
            ->method('getTokenType')
            ->will($this->returnValue($type));

        $this->accessToken->expects($this->atLeastOnce())
            ->method('getAccessToken')
            ->will($this->returnValue($token));
    }

    protected function initResponse($status, $json)
    {
        $this->expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($status));
        $this->expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($json));
    }

    protected function initBuilder($requestBody, $intent)
    {
        $this->builder->expects($this->once())
            ->method('build')
            ->will($this->returnValue(json_encode($requestBody)));
    }

    protected function initClient($requestBody, $uri = '/v1/payments/payment')
    {
        $this->client->expects($this->atLeastOnce())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.$uri,
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                json_encode($requestBody),
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));
    }
}
