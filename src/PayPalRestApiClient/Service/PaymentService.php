<?php

namespace PayPalRestApiClient\Service;

use Guzzle\Http\Client;
use PayPalRestApiClient\Model\AccessToken;
use Guzzle\Http\Exception\ClientErrorResponseException;
use PayPalRestApiClient\Builder\PaymentBuilder;

class PaymentService
{
    protected $client;
    protected $baseUrl;
    protected $returnUrl;
    protected $cancelUrl;
    protected $debug;

    public function __construct(Client $client, $baseUrl, $returnUrl, $cancelUrl, $debug = false)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->returnUrl = $returnUrl;
        $this->cancelUrl = $cancelUrl;
        $this->debug = $debug;
    }

    public function create(
        AccessToken $accessToken,
        $total,
        $currency,
        $description = null,
        $items = array(),
        $shippingAddress = array()
    ) {
        $data = array(
            'intent' => 'sale',
            'payer' => array(
                'payment_method' => 'paypal'
            ),
            'redirect_urls' => array(
                'return_url' => $this->returnUrl,
                'cancel_url' => $this->cancelUrl
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => $total,
                        'currency' => $currency,
                    ),
                    'description' => $description
                )
            ),
        );

        if ( ! empty($items)) {
            $data['transactions'][0]['item_list']['items'] = $items;
        }

        if ( ! empty($shippingAddress)) {
            $data['transactions'][0]['item_list']['shipping_address'] = $shippingAddress;
        }

        $requestBody = json_encode($data);

        $request = $this->client->createRequest(
            'POST',
            $this->baseUrl.'/v1/payments/payment',
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => $accessToken->getTokenType().' '.$accessToken->getAccessToken(),
                'Content-Type' => 'application/json'
            ),
            $requestBody,
            array(
                'debug' => $this->debug
            )
        );

        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new PaymentException(
                "Cannot create payment: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (201 != $response->getStatusCode()) {
            throw new PaymentException(
                "Cannot create payment: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;
    }
}