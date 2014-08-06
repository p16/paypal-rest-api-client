<?php

namespace PayPalRestApiClient\Service;

use Guzzle\Http\Client;
use PayPalRestApiClient\Model\AccessToken;
use PayPalRestApiClient\Model\Payment;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
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

    public function capture(
        AccessToken $accessToken,
        Payment $payment,
        $payerId
    ) {
        $request = $this->client->createRequest(
            'POST',
            $payment->getExecuteUrl(),
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => $accessToken->getTokenType().' '.$accessToken->getAccessToken(),
                'Content-Type' => 'application/json'
            ),
            '{"payer_id":"'.$payerId.'"}',
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
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (200 != $response->getStatusCode()) {
            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;
    }

    public function authorize(AccessToken $accessToken, PaymentRequestBodyBuilder $paymentRequestBodyBuilder)
    {
        $paymentRequestBodyBuilder->setIntent('authorize');

        $request = $this->buildRequest($accessToken, $paymentRequestBodyBuilder);

        return $this->doSend($request);
    }

    public function create(AccessToken $accessToken, PaymentRequestBodyBuilder $paymentRequestBodyBuilder)
    {
        $paymentRequestBodyBuilder->setIntent('sale');

        $request = $this->buildRequest($accessToken, $paymentRequestBodyBuilder);

        return $this->doSend($request);
    }

    protected function buildRequest(AccessToken $accessToken, PaymentRequestBodyBuilder $paymentRequestBodyBuilder)
    {
        $data = $paymentRequestBodyBuilder->build();
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

        return $request;
    }

    protected function doSend($request)
    {
        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (201 != $response->getStatusCode()) {
            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;        
    }
}