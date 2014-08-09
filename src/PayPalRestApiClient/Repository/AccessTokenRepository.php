<?php

namespace PayPalRestApiClient\Repository;

use Guzzle\Http\Client;
use PayPalRestApiClient\Model\AccessToken;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * 
 */
class AccessTokenRepository
{
    use \PayPalRestApiClient\Traits\RequestSender;

    protected $client;
    protected $baseUrl;
    protected $debug = false;

    public function __construct(Client $client, $baseUrl, $debug = false)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->debug = $debug;
    }

    protected function getClient()
    {
        return $this->client;
    }

    /**
     * Calling twice the getAccessToken method with the same credential will give you two different token.
     * For the time being, expires_in is not taken into consideration.
     */
    public function getAccessToken($clientId, $secret)
    {
        $request = $this->client->createRequest(
            'POST',
            $this->baseUrl.'/v1/oauth2/token',
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'grant_type=client_credentials',
            array(
                'auth' => array($clientId, $secret),
                'debug' => $this->debug
            )
        );

        $response = $this->send($request, $acceptedStatusCode = 200, 'Error requesting token:');

        $data = json_decode($response->getBody(), true);

        return AccessToken::fromArray($data);
    }
}
