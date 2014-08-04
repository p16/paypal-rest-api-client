PHP PayPal REST API Client
==========================

This library wants to be a client for the [PayPal REST API](https://developer.paypal.com/docs/api/).
There is an official SDK and you can find it [here](https://github.com/paypal/rest-api-sdk-php).
I tried it and I tried to [contribute on it](https://github.com/p16/rest-api-sdk-php) (the tests were calling the actual paypal sandbox). 
But I needed a simpler and OOP library, and I do not have the time to contribute as much as I think is needed on the official one, therefore I did this one.

Feedback, PR and contribution are welcome.


Features
--------

At the moment the only [PayPal REST API](https://developer.paypal.com/docs/api/) calls implemented are:

- Request a Access Token: https://developer.paypal.com/docs/api/#authentication--headers
- Create a payment only with "paypal" payment_method: https://developer.paypal.com/docs/api/#create-a-payment 
- Execute a payment: https://developer.paypal.com/docs/api/#execute-an-approved-paypal-payment


Installation
------------

1. Donwload the repository

2. Run composer install

    composer install


Running tests
-------------

From the root folder run

    phpunit -c .


Using the library
-----------------

    $clientId = 'CLIENTID';
    $secret = 'SECRET';

    $this->debug = true;
    $this->baseUrl = 'https://api.sandbox.paypal.com';
    $this->returnUrl = 'http://example.com/success';
    $this->cancelUrl = 'http://example.com/cancel';
    $this->total = 15.00;
    $this->currency = 'EUR';
    $this->description = 'My fantastic transaction description';

    $this->shippingAddress = array(
        'recipient_name' => 'Fi Fi',
        'type' => 'residential',
        'line1' => 'Via del mare',
        'line2' => '',
        'city' => 'Milano',
        'country_code' => 'IT',
        'postal_code' => '60010',
        'state' => '',
        'phone' => '3213213211',
    );

    $this->items = array(
        array(
            'quantity' => 1,
            'name' => 'example',
            'price' => '5.00',
            'currency' => 'EUR',
            'sku' => '1',
        ),
        array(
            'quantity' => 1,
            'name' => 'example',
            'price' => '3.00',
            'currency' => 'EUR',
            'sku' => '2',
        ),
        array(
            'quantity' => 1,
            'name' => 'example',
            'price' => '7.00',
            'currency' => 'EUR',
            'sku' => '3',
        )
    );
    
    $this->client = new Client();

    $repo = new AccessTokenRepository(
        $this->client,
        'https://api.sandbox.paypal.com',
        true
    );
    $token = $repo->getAccessToken($clientId, $secret);

    $service = new PaymentService(
        $this->client,
        $this->baseUrl,
        $this->returnUrl,
        $this->cancelUrl,
        $this->debug
    );
    $payment = $service->create(
        $accessToken,
        $this->total,
        $this->currency,
        $this->description,
        $this->items,
        $this->shippingAddress
    );

    $redirectUrl = $payment->getApprovalUrl();

    /* redirects the user to $redirectUrl */
    /* coming back from PayPal http://example.com/success?token=EC-9VK533621R3302713&PayerID=CBMFXGW3CHM7Q */

    $payerID = $_GET['PayerID'];

    $service = new PaymentService(
        $this->client,
        $this->baseUrl,
        $this->returnUrl,
        $this->cancelUrl,
        $this->debug
    );
    $payment = $service->capture($accessToken, $payment, $payerId);

    var_dump($payment);
