<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Service\PaymentService;

class PaymentServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMultipleItems()
    {
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

        $status = 201;
        $json = '{"id":"PAY-15M32000F9063160GKPOXIAY","create_time":"2014-08-02T23:28:03Z","update_time":"2014-08-02T23:28:03Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"Fi Fi","line1":"Via del mare","city":"Milano","phone":"3213213211","postal_code":"60010","country_code":"IT"}}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-2BD64194W6159691D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY/execute","rel":"execute","method":"POST"}]}';

        $accessToken = $this->getMockBuilder('PayPalRestApiClient\Model\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();

        $accessToken->expects($this->atLeastOnce())
            ->method('getTokenType')
            ->will($this->returnValue('Bearer'));

        $accessToken->expects($this->atLeastOnce())
            ->method('getAccessToken')
            ->will($this->returnValue('123abc123abc'));

        $requestBody = json_encode(array(
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
                    'description' => $this->description,
                    'item_list' => array(
                        'items' => $this->items,
                        'shipping_address' => $this->shippingAddress
                    )
                )
            ),
        ));

        $expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();
        $expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($status));
        $expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($json));

        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
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
            )
            ->will($this->returnValue($expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($expectedRequest)
            ->will($this->returnValue($expectedResponse));


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

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
    }

    public function testCreateNoItems()
    {
        $this->debug = true;
        $this->baseUrl = 'https://api.sandbox.paypal.com';
        $this->returnUrl = 'http://example.com/success';
        $this->cancelUrl = 'http://example.com/cancel';
        $this->total = 14.77;
        $this->currency = 'EUR';
        $this->description = 'My fantastic transaction description';

        $status = 201;
        $json = '{"id":"PAY-74S36081BM7699248KPOPD5Q","create_time":"2014-08-02T14:13:10Z","update_time":"2014-08-02T14:13:10Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"14.77","currency":"EUR","details":{"subtotal":"14.77"}},"description":"My fantastic transaction description"}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-26339740WK411984R","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute","rel":"execute","method":"POST"}]}';

        $accessToken = $this->getMockBuilder('PayPalRestApiClient\Model\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();

        $accessToken->expects($this->atLeastOnce())
            ->method('getTokenType')
            ->will($this->returnValue('Bearer'));

        $accessToken->expects($this->atLeastOnce())
            ->method('getAccessToken')
            ->will($this->returnValue('123abc123abc'));

        $requestBody = json_encode(array(
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
        ));

        $expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();
        $expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($status));
        $expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($json));

        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
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
            )
            ->will($this->returnValue($expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($expectedRequest)
            ->will($this->returnValue($expectedResponse));


        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->create($accessToken, $this->total, $this->currency, $this->description);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('created', $payment->getState());
    }

    /**
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testCapturePayment()
    {
        $json = '{"id":"PAY-6T42818722685883WKPPAT6I","create_time":"2014-08-03T10:07:53Z","update_time":"2014-08-03T10:11:42Z","state":"approved","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"email":"verticesbuyer@example.com","first_name":"vertices","last_name":"buyer","payer_id":"CBMFXGW3CHM7Q","shipping_address":{"line1":"Via del mare","line2":"","city":"Milano","state":"","postal_code":"60010","country_code":"IT","phone":"3213213211","recipient_name":"Fi Fi"}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"vertices buyer","line1":"Via del mare","line2":"","city":"Milano","state":"","phone":"3213213211","postal_code":"60010","country_code":"IT"}},"related_resources":[{"sale":{"id":"4P624962P1332762G","create_time":"2014-08-03T10:07:53Z","update_time":"2014-08-03T10:11:42Z","amount":{"total":"15.00","currency":"EUR"},"state":"completed","parent_payment":"PAY-6T42818722685883WKPPAT6I","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/sale/4P624962P1332762G","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/sale/4P624962P1332762G/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I","rel":"self","method":"GET"}]}';
        $status = 200;

        $this->debug = true;
        $this->baseUrl = 'https://api.sandbox.paypal.com';
        $this->returnUrl = 'http://example.com/success';
        $this->cancelUrl = 'http://example.com/cancel';

        $expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();

        $accessToken = $this->getMockBuilder('PayPalRestApiClient\Model\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();

        $accessToken->expects($this->atLeastOnce())
            ->method('getTokenType')
            ->will($this->returnValue('Bearer'));

        $accessToken->expects($this->atLeastOnce())
            ->method('getAccessToken')
            ->will($this->returnValue('123abc123abc'));

        $requestBody = '{ "payer_id" : "CBMFXGW3CHM7Q" }';

        $expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();
        $expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($status));
        $expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($json));

        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment/PAY-6T42818722685883WKPPAT6I/execute',
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
            )
            ->will($this->returnValue($expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($expectedRequest)
            ->will($this->returnValue($expectedResponse));

        $payment = $this->getMockBuilder('PayPalRestApiClient\Model\Payment')
            ->disableOriginalConstructor()
            ->getMock();
        $payment->expects($this->once())
            ->method('getExecuteUrl')
            ->will($this->returnValue(
                'https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I/execute'
            ));

        $payerId = "CBMFXGW3CHM7Q";


        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->capture($accessToken, $payment, $payerId);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('approved', $payment->getState());
    }

    /**
     * @see https://developer.paypal.com/docs/integration/direct/accept-credit-cards/
     */
    public function testCreateCreditCardPayment()
    {
        $this->markTestIncomplete();
    }

    /**
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testAuthorizePayment()
    {
        $this->markTestIncomplete();
    }
}



/*
curl -v https://api.sandbox.paypal.com/v1/payments/payment \
-H "Content-Type:application/json" \
-H "Authorization: Bearer {accessToken}" \
-d '{
  "intent":"sale",
  "payer":{
    "payment_method":"credit_card",
    "funding_instruments":[
      {
        "credit_card":{
          "number":"4417119669820331",
          "type":"visa",
          "expire_month":11,
          "expire_year":2018,
          "cvv2":"874",
          "first_name":"Betsy",
          "last_name":"Buyer",
          "billing_address":{
            "line1":"111 First Street",
            "city":"Saratoga",
            "state":"CA",
            "postal_code":"95070",
            "country_code":"US"
          }
        }
      }
    ]
  },
  "transactions":[
    {
      "amount":{
        "total":"7.47",
        "currency":"USD",
        "details":{
          "subtotal":"7.41",
          "tax":"0.03",
          "shipping":"0.03"
        }
      },
      "description":"This is the payment transaction description."
    }
  ]
}'
*/
