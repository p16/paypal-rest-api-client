<?php

namespace PayPalRestApiClient\Model;

use PayPalRestApiClient\Exception\AccessTokenException;

/**
 * The AccessToken class represents a paypal access token object
 */
class AccessToken
{
    use \PayPalRestApiClient\Traits\PaypalData;

    protected $scope;
    protected $accessToken;
    protected $tokenType;
    protected $appId;
    protected $expiresIn;

    /**
     * Construct 
     *
     * @param string $accessToken not null
     * @param string $tokenType not null
     * @param string $appId not null 
     * @param string $expiresIn not null
     * @param string $scope not null
     *
     * @see https://developer.paypal.com/docs/api/#authentication--headers
     */
    public function __construct($accessToken, $tokenType, $appId, $expiresIn, $scope)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->appId = $appId;
        $this->expiresIn = $expiresIn;
        $this->scope = $scope;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    public function getScope()
    {
        return $this->scope;
    }
}