<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Model\AccessToken;

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $data = array(
            'scope' => "https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*",
            'access_token' => "A015RBZpQe4cp00uD0T.hSO5W9YtuO-0jHtnSCjSt-aCzyQ",
            'token_type' => "Bearer",
            'app_id' => "APP-80W284485P519543T",
            'expires_in' => 28800
        );
        $accessToken = AccessToken::fromArray($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\AccessToken', $accessToken);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\AccessTokenException
     * @expectedExceptionMessage Mandatory data missing for: scope, access_token, token_type, app_id, expires_in
     */
    public function testFromArrayValidationNoScope()
    {
        $data = array(
        );
        $accessToken = AccessToken::fromArray($data);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\AccessTokenException
     * @expectedExceptionMessage access_token is mandatory and should not be empty
     */
    public function testFromArrayValidationEmptyAccessToken()
    {
        $data = array(
            'scope' => "https://uri.paypal.com/services/subscriptions https://api.paypal.com/v1/payments/.* https://api.paypal.com/v1/vault/credit-card https://uri.paypal.com/services/applications/webhooks openid https://uri.paypal.com/services/invoicing https://api.paypal.com/v1/vault/credit-card/.*",
            'access_token' => "",
            'token_type' => "Bearer",
            'app_id' => "APP-80W284485P519543T",
            'expires_in' => 28800
        );
        $accessToken = AccessToken::fromArray($data);
    }
}
