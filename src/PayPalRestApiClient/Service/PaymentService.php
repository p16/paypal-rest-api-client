<?php

namespace PayPalRestApiClient\Service;

use Guzzle\Http\Client;
use PayPalRestApiClient\Model\AccessToken;
use PayPalRestApiClient\Model\Payment;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
use PayPalRestApiClient\Builder\CaptureBuilder;
use PayPalRestApiClient\Builder\AuthorizationBuilder;
use Guzzle\Http\Exception\ClientErrorResponseException;
use PayPalRestApiClient\Builder\PaymentBuilder;
use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Validator\PayPalJsonSchemaValidator;

class PaymentService
{
    use \PayPalRestApiClient\Traits\RequestSender;

    protected $client;
    protected $baseUrl;
    protected $paymentRequestBodyBuilder;
    protected $debug;

    public function __construct(
        Client $client,
        PaymentRequestBodyBuilder $paymentRequestBodyBuilder,
        $baseUrl,
        $debug = false
    ) {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->paymentRequestBodyBuilder = $paymentRequestBodyBuilder;
        $this->debug = $debug;
    }

    protected function getClient()
    {
        return $this->client;
    }

    public function execute(
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

        $response = $this->send($request, 200, "Payment error:");
        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;
    }

    public function capture(AccessToken $accessToken, Payment $payment, $isFinalCapture = true)
    {
        $amount = $payment->getAmount();
        $data = array(
            'amount' => array(
                'total' => $amount->getTotal(),
                'currency' => $amount->getCurrency()
            ),
            'is_final_capture' => $isFinalCapture
        );

        $request = $this->client->createRequest(
            'POST',
            $payment->getCaptureUrls()[0],
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => $accessToken->getTokenType().' '.$accessToken->getAccessToken(),
                'Content-Type' => 'application/json'
            ),
            json_encode($data),
            array(
                'debug' => $this->debug
            )
        );

        $response = $this->send($request, 200, "Payment error:");
        $data = json_decode($response->getBody(), true);

        $captureBuilder = new CaptureBuilder();
        $capture = $captureBuilder->build($data);

        return $capture;
    }

    public function authorize(
        AccessToken $accessToken,
        $payer,
        $urls,
        $transactions
    ) {

        $requestBody = $this->paymentRequestBodyBuilder->build(
            'authorize',
            $payer,
            $urls,
            $transactions
        );

        $this->assertReqeustJsonSchema($requestBody);

        $request = $this->buildRequest($accessToken, $requestBody);

        $response = $this->send($request, 201, "Payment error:");

        $data = json_decode($response->getBody(), true);
        if ('credit_card' === $data['payer']['payment_method']) {

            $authorizationBuilder = new AuthorizationBuilder();
            $authorization = $authorizationBuilder->build($data);

            return $authorization;        
        }

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;        
    }

    public function create(
        AccessToken $accessToken,
        $payer,
        $urls,
        $transactions
    ) {
        $requestBody = $this->paymentRequestBodyBuilder->build(
            'sale',
            $payer,
            $urls,
            $transactions
        );

        $this->assertReqeustJsonSchema($requestBody);

        $request = $this->buildRequest($accessToken, $requestBody);

        $response = $this->send($request, 201, "Payment error:");

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;        
    }

    protected function assertReqeustJsonSchema($requestBody)
    {
        $validator = new PayPalJsonSchemaValidator('payment_request.json', json_decode($requestBody));
        if ( ! $validator->isValid()) {
            $errorString = '';
            foreach ($validator->getErrors() as $error) {
                $errorString .= sprintf("[%s] %s - ", $error['property'], $error['message']);
            }

            throw new BuilderException("Request body is not valid: $errorString");
        }
    }

    protected function buildRequest(AccessToken $accessToken, $requestBody)
    {
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
}