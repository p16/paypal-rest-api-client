<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Service\PaymentService;

class PaymentServiceCreditCardMethodTest extends PaymentServiceTestCase
{
    /**
     * This payment is direct. No other action needed. 
     *
     * @see https://developer.paypal.com/docs/integration/direct/accept-credit-cards/
     */
    public function testCreateCreditCardDirectPayment()
    {
        $status = 201;
        $json = '{"id":"PAY-1P673290VA6629244KPP7RQY","create_time":"2014-08-04T21:18:59Z","update_time":"2014-08-04T21:19:17Z","state":"approved","intent":"sale","payer":{"payment_method":"credit_card","funding_instruments":[{"credit_card":{"type":"visa","number":"xxxxxxxxxxxx0331","expire_month":"11","expire_year":"2018","first_name":"Betsy","last_name":"Buyer","billing_address":{"line1":"111 First Street","city":"Saratoga","state":"CA","postal_code":"95070","country_code":"US"}}}]},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","related_resources":[{"sale":{"id":"5GK13613UK831723G","create_time":"2014-08-04T21:18:59Z","update_time":"2014-08-04T21:19:17Z","amount":{"total":"15.00","currency":"EUR"},"state":"completed","parent_payment":"PAY-1P673290VA6629244KPP7RQY","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/sale/5GK13613UK831723G","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/sale/5GK13613UK831723G/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-1P673290VA6629244KPP7RQY","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-1P673290VA6629244KPP7RQY","rel":"self","method":"GET"}]}';

        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array(
            'intent' => 'sale',
            'payer' => array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array(
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
                ),
            ),
            'redirect_urls' => array(
                'return_url' => $this->returnUrl,
                'cancel_url' => $this->cancelUrl
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => $this->total,
                        'currency' => $this->currency,
                    ),
                    'description' => $this->description
                )
            ),
        );
        $this->initBuilder($requestBody, 'sale');
        $this->initClient($requestBody);

        $payer = $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $urls = array(
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl
        );
        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');

        $payment = $this->service->create(
            $this->accessToken,
            $payer,
            $urls,
            array($transaction)
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('approved', $payment->getState());
    }

    /**
     * Autorizing a credit card payment and then capture it is a 2 step process:
     * #1 Call the authorize method: you will receive a PaymentAuthorization object
     *
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testAuthorizePaymentCreditCardMethod()
    {
        $status = 201;
        $json = '{"id":"PAY-2LS84841MB1756502KPSQJJA","create_time":"2014-08-08T17:11:00Z","update_time":"2014-08-08T17:11:19Z","state":"approved","intent":"authorize","payer":{"payment_method":"credit_card","funding_instruments":[{"credit_card":{"type":"visa","number":"xxxxxxxxxxxx0331","expire_month":"11","expire_year":"2018","first_name":"Betsy","last_name":"Buyer","billing_address":{"line1":"111 First Street","city":"Saratoga","state":"CA","postal_code":"95070","country_code":"US"}}}]},"transactions":[{"amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"description":"my transaction","related_resources":[{"authorization":{"id":"6JK78052MJ7446007","create_time":"2014-08-08T17:11:00Z","update_time":"2014-08-08T17:11:19Z","amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"state":"authorized","parent_payment":"PAY-2LS84841MB1756502KPSQJJA","valid_until":"2014-09-06T17:11:00Z","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/capture","rel":"capture","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/void","rel":"void","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-2LS84841MB1756502KPSQJJA","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-2LS84841MB1756502KPSQJJA","rel":"self","method":"GET"}]}';

        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array(
            'intent' => 'authorize',
            'payer' => array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array(
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
                ),
            ),
            'redirect_urls' => array(
                'return_url' => $this->returnUrl,
                'cancel_url' => $this->cancelUrl
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => $this->total,
                        'currency' => $this->currency,
                    ),
                    'description' => $this->description
                )
            ),
        );
        $this->initBuilder($requestBody, 'authorize');
        $this->initClient($requestBody);

        $payer = $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $urls = array(
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl
        );
        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');

        $paymentAuthorization = $this->service->authorize(
            $this->accessToken,
            $payer,
            $urls,
            array($transaction)
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\CreditCardPaymentAuthorization', $paymentAuthorization);
        $this->assertEquals('approved', $paymentAuthorization->getState());
        $this->assertEquals('authorize', $paymentAuthorization->getIntent());
        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/capture',
            $paymentAuthorization->getCaptureUrl()
        );

        return $paymentAuthorization;
    }

    /**
     * #2 Call the capture method passing the authorization object
     *
     * @depends testAuthorizePaymentCreditCardMethod
     *
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testCaptureCreditCardPayment($paymentAuthorization)
    {
        $json = '{"id":"6BA17599X0950293U","create_time":"2013-05-06T22:32:24Z","update_time":"2013-05-06T22:32:25Z","amount":{"total":"4.54","currency":"USD"},"is_final_capture":true,"state":"completed","parent_payment":"PAY-44664305570317015KGEC5DI","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/capture/6BA17599X0950293U","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/capture/6BA17599X0950293U/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/5RA45624N3531924N","rel":"authorization","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-44664305570317015KGEC5DI","rel":"parent_payment","method":"GET"}]}';
        $status = 200;
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array("amount" => array("total" => "12.35", "currency" => "EUR"), "is_final_capture" => true);
        $this->initClient($requestBody, '/v1/payments/authorization/6JK78052MJ7446007/capture');

        $capture = $this->service->capture($this->accessToken, $paymentAuthorization);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Capture', $capture);
        $this->assertEquals('completed', $capture->getState());
        $this->assertEquals(true, $capture->isFinalCapture());
    }
}
