<?php

namespace PayPalRestApiClient\Repository;

use Guzzle\Http\Client;
use PayPalRestApiClient\Model\AccessToken;
use PayPalRestApiClient\Exception\AccessTokenException;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * 
 */
class AccessTokenRepository
{
    protected $client;
    protected $baseUrl;
    protected $debug = false;

    public function __construct(Client $client, $baseUrl, $debug = false)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->debug = $debug;
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

        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new AccessTokenException(
                "Cannot retrieve access token: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (200 != $response->getStatusCode()) {

            throw new AccessTokenException(
                "Cannot retrieve access token: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        return AccessToken::fromArray($data);
    }
}
