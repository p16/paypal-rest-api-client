PHP PayPal REST API Client
==========================

[![Latest Stable Version](https://poser.pugx.org/p16/paypal-rest-api-client/v/stable.svg)](https://packagist.org/packages/p16/paypal-rest-api-client) [![Total Downloads](https://poser.pugx.org/p16/paypal-rest-api-client/downloads.svg)](https://packagist.org/packages/p16/paypal-rest-api-client) [![Latest Unstable Version](https://poser.pugx.org/p16/paypal-rest-api-client/v/unstable.svg)](https://packagist.org/packages/p16/paypal-rest-api-client) [![License](https://poser.pugx.org/p16/paypal-rest-api-client/license.svg)](https://packagist.org/packages/p16/paypal-rest-api-client)

This library wants to be a PHP client for the [PayPal REST API](https://developer.paypal.com/docs/api/).

There is an official SDK and you can find it [here](https://github.com/paypal/rest-api-sdk-php).

I tried it and I tried to [contribute on it](https://github.com/p16/rest-api-sdk-php) (the tests were calling the actual paypal sandbox). 
But I needed a simpler and OOP library, and I did not have the time to contribute as much as I think is needed on the official one, therefore I did this one.

I'm working on a first stable realease that should be out before August 15th.

Feedback, PR and contribution are welcome.



Features
--------

At the moment the only [PayPal REST API](https://developer.paypal.com/docs/api/) calls implemented are:

- Require an access token: https://developer.paypal.com/docs/api/#authentication--headers
- Create a payment (only with "paypal" and "credit_card" payment methods): https://developer.paypal.com/docs/api/#create-a-payment 
- Execute a payment: https://developer.paypal.com/docs/api/#execute-an-approved-paypal-payment
- Authorize and capture a payment: https://developer.paypal.com/docs/integration/direct/capture-payment/ (only with credit card, not with paypal payment method)
- Validation of json schema for payment requests: [Json Schema](http://json-schema.org/), [PHP Json Schema validator](https://github.com/justinrainbow/json-schema), [PayPal REST API objects](https://developer.paypal.com/docs/api/)

Installation
------------

Add the following dependency to your composer.json


    "p16/paypal-rest-api-client": "dev-master"


Run

    composer update p16/paypal-rest-api-client



Running tests
-------------

Donwload the repository

Run

    composer install


From the root folder run

    phpunit -c .


Documentation
-------------

**Using the library**

    $this->baseUrl = 'https://api.sandbox.paypal.com';
    $this->returnUrl = 'http://example.com/success';
    $this->cancelUrl = 'http://example.com/cancel';

    $this->client = new Client();

    $repo = new AccessTokenRepository(
        $this->client,
        $this->baseUrl
    );
    $accessToken = $repo->getAccessToken($clientId, $secret);

    $paymentRequestBodyBuilder = new PaymentRequestBodyBuilder();

    $paymentService = new PaymentService(
        $this->client,
        $paymentRequestBodyBuilder,
        $this->baseUrl
    );

    $this->item_list = array(
        'items' => array(
            array(
                'quantity' => 1,
                'name' => 'product name',
                'price' => '12.35',
                'currency' => 'EUR',
                'sku' => '1233456789',
            ),
        )
    );

    $amount = new Amount('EUR', '12.35');
    $transaction = new Transaction($amount, 'my transaction', $this->item_list);

    $payment = $paymentService->create(
        $accessToken,
        new Payer('paypal'),
        array(
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl
        ),
        array($transaction)
    );

    $_SESSION['payment_data'] = $payment->getPaypalData();
    // or
    // $_SESSION['payment_data'] = serialize($payment);

    $redirectUrl = $payment->getApprovalUrl();

    /* redirects the user to $redirectUrl */
    /* coming back from PayPal http://example.com/success?token=EC-9VK533621R3302713&PayerID=CBMFXGW3CHM7Q */

    $payerId = $_GET['PayerID'];

    $paymentRequestBodyBuilder = new PaymentRequestBodyBuilder();

    $paymentService = new PaymentService(
        $this->client,
        $paymentRequestBodyBuilder,
        $this->baseUrl
    );

    $paymentBuilder = new PaymentBuilder();
    $originalPayment = $paymentBuilder->build($_SESSION['payment_data']);
    // or
    // $originalPayment = unserialize($_SESSION['payment_data']);


    $payment = $service->execute($accessToken, $originalPayment, $payerId);

    var_dump($payment);


**PayPal Json schema validation**

A [Json Schema validator](https://github.com/justinrainbow/json-schema) is used to validate a call request body. You can find all the available schema definition [here](src/PayPalRestApiClient/Validator/schema).


**TO-DO for release 0.1**

1. Write classes and methods documentation
2. As of today the PAyPal REST API is not supporting multiple transaction (yet). Refactoring classes using transactions arrays to consider only the first one (as long as multiple transaction are not supported) (?)
3. Move PayPalJsonSchemaValidator into a standalone repo (?)

**TO-DO for release 0.2**

1. Make the builder aware of the PayPalJsonSchemaValidator, so that they can validate what they are building when building from/to json

2. Add validation to model classes (?)

3. Add authorization and capture of a payment with paypal payment method

