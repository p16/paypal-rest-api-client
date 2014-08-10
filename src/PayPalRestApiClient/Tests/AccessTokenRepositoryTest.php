<?php

namespace PayPalRestApiClient\Tests;

use Guzzle\Http\Exception\ClientErrorResponseException;
use PayPalRestApiClient\Repository\AccessTokenRepository;

class AccessTokenRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAuthenticationToken()
    {
        $clientId = 'AWeN5RAsJJLTdwGYLFnCb2FYhLZ275Omc5c1PJQWoiDyElIr_emldcojjwpW';
        $secret = 'EIxa8RBPOytHuO_v38RJdIqTjhq87GLuA6eZnJ24wUV_4AM6AIK8vR0iHSA4';

        $expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();
        $expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $json = '{"scope":"https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*","access_token":"A015RBZpQe4cp00uD0T.hSO5W9YtuO-0jHtnSCjSt-aCzyQ","token_type":"Bearer","app_id":"APP-80W284485P519543T","expires_in":28800}';
        $expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($json));


        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                'https://api.sandbox.paypal.com/v1/oauth2/token',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ),
                'grant_type=client_credentials',
                array('auth' => array($clientId, $secret), 'debug' => true)
            )
            ->will($this->returnValue($expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($expectedRequest)
            ->will($this->returnValue($expectedResponse));


        $repo = new AccessTokenRepository(
            $this->client,
            'https://api.sandbox.paypal.com',
            true
        );
        $token = $repo->getAccessToken($clientId, $secret);

        $this->assertEquals(
            'A015RBZpQe4cp00uD0T.hSO5W9YtuO-0jHtnSCjSt-aCzyQ',
            $token->getAccessToken()
        );
        $this->assertEquals(
            'Bearer',
            $token->getTokenType()
        );
        $this->assertEquals(
            'APP-80W284485P519543T',
            $token->getAppId()
        );
        $this->assertEquals(
            28800,
            $token->getExpiresIn()
        );
        $this->assertEquals(
            'https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*',
            $token->getScope()
        );
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\CallException
     * @expectedExceptionMessage Error requesting token: response status 401 Unauthorized,  reason: error: invalid_client, error_description: Client secret does not match for this client
     */
    public function testWrongResponseGivenShouldThrowPayPalCallException()
    {
        $clientId = 'example';
        $secret = 'example';

        $expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody', 'getReasonPhrase'))
            ->getMock();
        $expectedResponse->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue('Unauthorized'));
        $expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(401));
        $expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue('{"error":"invalid_client","error_description":"Client secret does not match for this client"}'));

        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                'https://api.sandbox.paypal.com/v1/oauth2/token',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ),
                'grant_type=client_credentials',
                array('auth' => array($clientId, $secret), 'debug' => true)
            )
            ->will($this->returnValue($expectedRequest));

        $exception = new ClientErrorResponseException();
        $exception->setResponse($expectedResponse);
        $this->client->expects($this->once())
            ->method('send')
            ->with($expectedRequest)
            ->will($this->throwException($exception));

        $repo = new AccessTokenRepository(
            $this->client,
            'https://api.sandbox.paypal.com',
            true
        );
        $token = $repo->getAccessToken($clientId, $secret);
    }
}
