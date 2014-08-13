Direct payment with paypal method
=================================

[PayPal documentation](https://developer.paypal.com/docs/api/#create-a-payment)

    $this->baseUrl = 'https://api.sandbox.paypal.com';
    $this->returnUrl = 'http://example.com/success';
    $this->cancelUrl = 'http://example.com/cancel';

    $this->client = new Client();

    $repo = new AccessTokenRepository(
        $this->client,
        $this->baseUrl
    );
    $accessToken = $repo->getAccessToken($clientId, $secret);

    $paymentService = new PaymentService(
        $this->client,
        new PaymentRequestBodyBuilder(),
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

    $paymentService = new PaymentService(
        $this->client,
        new PaymentRequestBodyBuilder(),
        $this->baseUrl
    );

    $paymentBuilder = new PaymentBuilder();
    $originalPayment = $paymentBuilder->build($_SESSION['payment_data']);
    // or
    // $originalPayment = unserialize($_SESSION['payment_data']);

    $payment = $service->execute($accessToken, $originalPayment, $payerId);

    /* 'approved' === $payment->getState() */

