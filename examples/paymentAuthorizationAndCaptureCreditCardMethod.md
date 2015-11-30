Payment authorization and capture with credit card method
=========================================================

[PayPal documentation](https://developer.paypal.com/docs/integration/direct/capture-payment/)

```php
<?php

use Guzzle\Http\Client;
use PayPalRestApiClient\Repository\AccessTokenRepository;
use PayPalRestApiClient\Service\PaymentService;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Payer;

$baseUrl = 'https://api.sandbox.paypal.com';
$returnUrl = 'http://example.com/success';
$cancelUrl = 'http://example.com/cancel';

$client = new Client();

$repo = new AccessTokenRepository(
    $client,
    $baseUrl
);
$accessToken = $repo->getAccessToken($clientId, $secret);

$paymentService = new PaymentService(
    $client,
    new PaymentRequestBodyBuilder(),
    $baseUrl
);

$itemList = array(
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
$transaction = new Transaction($amount, 'my transaction', $itemList);
$payer = new Payer(
    'credit_card',
    array(
        array(
            'credit_card' => array(
                'number' => '4417119669820331',
                'type' => 'visa',
                'expire_month' => 11,
                'expire_year' => 2018,
                'cvv2' => '874',
                'first_name' => 'Betsy',
                'last_name' => 'Buyer',
                'billing_address' => array(
                    'line1' => '111 First Street',
                    'city' => 'Saratoga',
                    'state' => 'CA',
                    'postal_code' => '95070',
                    'country_code' => 'US'
                )
            )
        )
    )
);

$paymentAuthorization = $paymentService->authorize(
    $accessToken,
    $payer,
    array(
        'return_url' => $returnUrl,
        'cancel_url' => $cancelUrl
    ),
    array($transaction)
);

/* 'approved' === $paymentAuthorization->getState() */

/* To capture the payment: */

$paymentService = new PaymentService(
    $client,
    new PaymentRequestBodyBuilder(),
    $baseUrl
);

$capture = $paymentService->capture($accessToken, $paymentAuthorization);

/* 'completed' === $capture->getState() */
```
