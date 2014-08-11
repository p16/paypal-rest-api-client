<?php

namespace PayPalRestApiClient\Repository;

use Guzzle\Http\Client;
use PayPalRestApiClient\Builder\AccessTokenBuilder;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * The AccessTokenRepository class has the responsability of retriving an access token given a pai of cliendId and clientSecret
 *
 * @see https://developer.paypal.com/docs/api/#authentication--headers
 */
class AccessTokenRepository
{
    use \PayPalRestApiClient\Traits\RequestSender;

    protected $client;
    protected $baseUrl;
    protected $debug = false;

    /**
     * Construct 
     *
     * @param Guzzle\Http\Client $client not null
     * @param string $baseUrl not null
     * @param boolean $debug default false
     */
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
     * Returns an access token object if paypal accepts the given credential
     * 
     * For the time being, expires_in is not taken into consideration.
     * Calling twice the getAccessToken method with the same credential will give you two different token.
     *
     * @param string $clientId not null
     * @param string $secret not null
     * 
     * @return PayPalRestApiClient\Model\AccessToken
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

        $builder = new AccessTokenBuilder();

        return $builder->build($data);
    }
}
