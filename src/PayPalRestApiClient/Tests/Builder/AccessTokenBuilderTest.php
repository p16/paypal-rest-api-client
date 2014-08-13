<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\AccessTokenBuilder;

class AccessTokenBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new AccessTokenBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'scope' => "https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*",
            'access_token' => "A015RBZpQe4cp00uD0T.hSO5W9YtuO-0jHtnSCjSt-aCzyQ",
            'token_type' => "Bearer",
            'app_id' => "APP-80W284485P519543T",
            'expires_in' => 28800
        );
        $accessToken = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\AccessToken', $accessToken);
        $this->assertEquals(
            'https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*',
            $accessToken->getScope()
        );
        $this->assertEquals(
            'A015RBZpQe4cp00uD0T.hSO5W9YtuO-0jHtnSCjSt-aCzyQ',
            $accessToken->getAccesstoken()
        );
        $this->assertEquals('Bearer', $accessToken->getTokenType());
        $this->assertEquals(
            'APP-80W284485P519543T',
            $accessToken->getAppId()
        );
        $this->assertEquals(
            '28800',
            $accessToken->getExpiresIn()
        );
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory keys missing for PayPalRestApiClient\Builder\AccessTokenBuilder: scope, access_token, token_type, app_id, expires_in
     */
    public function testBuildValidationNoScope()
    {
        $data = array();
        $accessToken = $this->builder->build($data);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage access_token is mandatory and should not be empty
     */
    public function testBuildValidationEmptyAccessToken()
    {
        $data = array(
            'scope' => "https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*",
            'access_token' => "",
            'token_type' => "Bearer",
            'app_id' => "APP-80W284485P519543T",
            'expires_in' => 28800
        );
        $accessToken = $this->builder->build($data);
    }
}
