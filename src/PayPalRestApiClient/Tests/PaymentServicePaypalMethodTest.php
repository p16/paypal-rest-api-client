<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Service\PaymentService;

class PaymentServicePaypalMethodTest extends PaymentServiceTestCase
{
    /**
     * Using the paypal method to create a payment and then execute it, it is a 2 steps process:
     * #1 Create a payment
     *
     * @see https://developer.paypal.com/docs/api/#create-a-payment
     */
    public function testCreateDirectPayment()
    {
        $status = 201;
        $json = '{"id":"PAY-74S36081BM7699248KPOPD5Q","create_time":"2014-08-02T14:13:10Z","update_time":"2014-08-02T14:13:10Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description"}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-26339740WK411984R","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute","rel":"execute","method":"POST"}]}';
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array(
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
        $this->assertEquals('created', $payment->getState());

        return $payment;
    }

    /**
     * @depends testCreateDirectPayment
     *
     * #2 execute the payment when paypal redirects the user to your success url with the PayerID parameter
     *
     * @see https://developer.paypal.com/docs/api/#execute-an-approved-paypal-payment
     */
    public function testExecuteDirectPayment($payment)
    {
        $json = '{"id":"PAY-6T42818722685883WKPPAT6I","create_time":"2014-08-03T10:07:53Z","update_time":"2014-08-03T10:11:42Z","state":"approved","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"email":"verticesbuyer@example.com","first_name":"vertices","last_name":"buyer","payer_id":"CBMFXGW3CHM7Q","shipping_address":{"line1":"Via del mare","line2":"","city":"Milano","state":"","postal_code":"60010","country_code":"IT","phone":"3213213211","recipient_name":"Fi Fi"}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"vertices buyer","line1":"Via del mare","line2":"","city":"Milano","state":"","phone":"3213213211","postal_code":"60010","country_code":"IT"}},"related_resources":[{"sale":{"id":"4P624962P1332762G","create_time":"2014-08-03T10:07:53Z","update_time":"2014-08-03T10:11:42Z","amount":{"total":"15.00","currency":"EUR"},"state":"completed","parent_payment":"PAY-6T42818722685883WKPPAT6I","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/sale/4P624962P1332762G","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/sale/4P624962P1332762G/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I","rel":"self","method":"GET"}]}';
        $status = 200;
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array("payer_id" => "CBMFXGW3CHM7Q");
        $this->initClient($requestBody, '/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute');

        $payerId = "CBMFXGW3CHM7Q";

        $payment = $this->service->execute($this->accessToken, $payment, $payerId);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('approved', $payment->getState());
    }

    /**
     * Using the paypal method to authorize a payment and then capture it, it is a 3 steps process:
     * #1 authorize the payment
     * #2 redirect the user to the approval url
     * 
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testAuthorizePaymentPaypalMethod()
    {
        #PAYPAL AUTHORIZATION: 1. authorize payment with "paypal" method
        #PAYPAL AUTHORIZATION: 2. redirect user to approve payment (assumption: user authorizes payment)

        $status = 201;
        $json = '{"id":"PAY-0R143116WW544010AKPU4P3I","create_time":"2014-08-12T07:53:17Z","update_time":"2014-08-12T07:53:17Z","state":"created","intent":"authorize","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"description":"my transaction","item_list":{"items":[{"name":"product name","sku":"1233456789","price":"12.35","currency":"EUR","quantity":"1"}]}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-42924620MK651460D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I/execute","rel":"execute","method":"POST"}]}';
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array(
            'intent' => 'authorize',
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

        $payment = $this->service->authorize(
            $this->accessToken,
            $payer,
            $urls,
            array($transaction)
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\PaypalPaymentAuthorization', $payment);
        $this->assertEquals('created', $payment->getState());
        $this->assertEquals('authorize', $payment->getIntent());
        $this->assertEquals(
            'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-42924620MK651460D',
            $payment->getApprovalUrl()
        );

        return $payment;
    }

    /**
     * #3 execute the paymennt as if it was a direct paypal payment, and get back an authorization
     * 
     * @depends testAuthorizePaymentPaypalMethod
     */
    public function testCompleteAuthorizationAndCaptureWithPaypalMethod($payment)
    {
        #PAYPAL AUTHORIZATION: 3. execute approvede payment
        $url = 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I/execute';
        $returnedJson = '{"id":"PAY-0R143116WW544010AKPU4P3I","create_time":"2014-08-12T07:53:17Z","update_time":"2014-08-12T07:58:20Z","state":"approved","intent":"authorize","payer":{"payment_method":"paypal","payer_info":{"email":"verticesbuyer@example.com","first_name":"vertices","last_name":"buyer","payer_id":"CBMFXGW3CHM7Q","shipping_address":{"line1":"Via Unit� Italia, 5783296","line2":"","city":"Napoli","state":"NAPOLI","postal_code":"80127","country_code":"IT","recipient_name":""}}},"transactions":[{"amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"description":"my transaction","item_list":{"items":[{"name":"product name","sku":"1233456789","price":"12.35","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"vertices buyer","line1":"Via Unit�, 5783296","line2":"","city":"Napoli","state":"NAPOLI","postal_code":"80127","country_code":"IT"}},"related_resources":[{"authorization":{"id":"11P01847VD5861647","create_time":"2014-08-12T07:53:17Z","update_time":"2014-08-12T07:58:20Z","amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"state":"authorized","parent_payment":"PAY-0R143116WW544010AKPU4P3I","valid_until":"2014-09-10T07:53:17Z","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/11P01847VD5861647","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/11P01847VD5861647/capture","rel":"capture","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/11P01847VD5861647/void","rel":"void","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/11P01847VD5861647/reauthorize","rel":"reauthorize","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I","rel":"self","method":"GET"}]}';
        $status = 200;
        $this->initResponse($status, $returnedJson);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array("payer_id" => "CBMFXGW3CHM7Q");
        $this->initClient($requestBody, '/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I/execute');

        $payerId = "CBMFXGW3CHM7Q";

        $paymentAuthorization = $this->service->execute($this->accessToken, $payment, $payerId);

        $this->assertInstanceOf('PayPalRestApiClient\Model\PaypalPaymentAuthorization', $paymentAuthorization);
        $this->assertEquals('approved', $paymentAuthorization->getState());

        return $paymentAuthorization;
    }

    /**
     * #4 capture the authorization as if it was from a credit card authorization payment
     * 
     * @depends testCompleteAuthorizationAndCaptureWithPaypalMethod
     */
    public function testAuthorizationCaptureWithPaypalMethod($paymentAuthorization)
    {
        #PAYPAL AUTHORIZATION: 4. capture the payment
        $url = 'https://api.sandbox.paypal.com/v1/payments/authorization/11P01847VD5861647/capture';
        $returnedJson = '{"id":"24150016S72705739","create_time":"2014-08-12T08:04:40Z","update_time":"2014-08-12T08:04:41Z","amount":{"total":"12.35","currency":"EUR"},"is_final_capture":true,"state":"completed","parent_payment":"PAY-0R143116WW544010AKPU4P3I","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/capture/24150016S72705739","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/capture/24150016S72705739/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/11P01847VD5861647","rel":"authorization","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I","rel":"parent_payment","method":"GET"}]}';
        $status = 200;
        $this->initResponse($status, $returnedJson);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array("amount" => array("total" => "12.35", "currency" => "EUR"), "is_final_capture" => true);
        $this->initClient($requestBody, '/v1/payments/authorization/11P01847VD5861647/capture');

        $capture = $this->service->capture($this->accessToken, $paymentAuthorization);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Capture', $capture);
        $this->assertEquals('completed', $capture->getState());
        $this->assertEquals(true, $capture->isFinalCapture());
    }
}
