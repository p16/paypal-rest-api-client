<?php

namespace PayPalRestApiClient\Model;

use PayPalRestApiClient\Exception\AccessTokenException;

class AccessToken
{
    protected $scope;
    protected $accessToken;
    protected $tokenType;
    protected $appId;
    protected $expiresIn;

    /**
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