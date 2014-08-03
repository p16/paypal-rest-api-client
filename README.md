PHP PayPal REST API Client
==========================

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
    $this->baseUrl = 'https://api.sandbox.paypal.com';
    $this->debug = true;

    $this->client = new Client();

    $repo = new AccessTokenRepository(
        $this->client,
        'https://api.sandbox.paypal.com',
        true
    );
    $token = $repo->getAccessToken($clientId, $secret);

    $requestBody = json_encode(array(
        'intent' => 'sale',
        'payer' => array(
            'payment_method' => 'paypal'
        ),
        'redirect_urls' => array(
            'return_url' => 'http://example.com/success',
            'cancel_url' => 'http://example.com/cancel'
        ),
        'transactions' => array(
            array(
                'amount' => array(
                    'total' => 15.00,
                    'currency' => 'EUR',
                ),
                'description' => 'My fantastic transaction description',
            )
        ),
    ));

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
