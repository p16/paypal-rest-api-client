<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Service\PaymentService;

class PaymentServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
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

        $this->accessToken = $this->getMockBuilder('PayPalRestApiClient\Model\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectedRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectedResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'getBody'))
            ->getMock();

        $this->client = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();
    }

    public function testCreateMultipleItems()
    {
        $status = 201;
        $json = '{"id":"PAY-15M32000F9063160GKPOXIAY","create_time":"2014-08-02T23:28:03Z","update_time":"2014-08-02T23:28:03Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"Fi Fi","line1":"Via del mare","city":"Milano","phone":"3213213211","postal_code":"60010","country_code":"IT"}}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-2BD64194W6159691D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY/execute","rel":"execute","method":"POST"}]}';
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $this->payer = array(
            'payment_method' => 'paypal'
        );

        $requestBody = json_encode(array(
            'intent' => 'sale',
            'payer' => $this->payer,
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

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                $requestBody,
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));


        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->create(
            $this->accessToken,
            $this->total,
            $this->currency,
            $this->payer,
            $this->description,
            $this->items,
            $this->shippingAddress
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
    }

    public function testCreateNoItems()
    {
        $status = 201;
        $json = '{"id":"PAY-74S36081BM7699248KPOPD5Q","create_time":"2014-08-02T14:13:10Z","update_time":"2014-08-02T14:13:10Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description"}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-26339740WK411984R","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute","rel":"execute","method":"POST"}]}';
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $this->payer = array(
            'payment_method' => 'paypal'
        );

        $requestBody = json_encode(array(
            'intent' => 'sale',
            'payer' => $this->payer,
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

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                $requestBody,
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));


        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->create(
            $this->accessToken,
            $this->total,
            $this->currency,
            $this->payer,
            $this->description
        );

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
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = '{ "payer_id" : "CBMFXGW3CHM7Q" }';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment/PAY-6T42818722685883WKPPAT6I/execute',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                $requestBody,
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));

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
        $payment = $service->capture($this->accessToken, $payment, $payerId);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('approved', $payment->getState());
    }

    /**
     * @see https://developer.paypal.com/docs/integration/direct/accept-credit-cards/
     */
    public function testCreateCreditCardPayment()
    {
        $status = 201;
        $json = '{"id":"PAY-1P673290VA6629244KPP7RQY","create_time":"2014-08-04T21:18:59Z","update_time":"2014-08-04T21:19:17Z","state":"approved","intent":"sale","payer":{"payment_method":"credit_card","funding_instruments":[{"credit_card":{"type":"visa","number":"xxxxxxxxxxxx0331","expire_month":"11","expire_year":"2018","first_name":"Betsy","last_name":"Buyer","billing_address":{"line1":"111 First Street","city":"Saratoga","state":"CA","postal_code":"95070","country_code":"US"}}}]},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","related_resources":[{"sale":{"id":"5GK13613UK831723G","create_time":"2014-08-04T21:18:59Z","update_time":"2014-08-04T21:19:17Z","amount":{"total":"15.00","currency":"EUR"},"state":"completed","parent_payment":"PAY-1P673290VA6629244KPP7RQY","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/sale/5GK13613UK831723G","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/sale/5GK13613UK831723G/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-1P673290VA6629244KPP7RQY","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-1P673290VA6629244KPP7RQY","rel":"self","method":"GET"}]}';

        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $this->payer = array(
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
        );

        $requestBody = json_encode(array(
            'intent' => 'sale',
            'payer' => $this->payer,
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

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                $requestBody,
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));

        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->create(
            $this->accessToken,
            $this->total,
            $this->currency,
            $this->payer,
            $this->description
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('approved', $payment->getState());
    }

    /**
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testAuthorizePaymentPaypalMethod()
    {
        $status = 201;
        $json = '{"id":"PAY-61B34806BY404941VKPQALTI","create_time":"2014-08-04T22:14:37Z","update_time":"2014-08-04T22:14:37Z","state":"created","intent":"authorize","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description"}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-61B34806BY404941VKPQALTI","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-6NC005862K2353254","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-61B34806BY404941VKPQALTI/execute","rel":"execute","method":"POST"}]}';
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $this->payer = array(
            'payment_method' => 'paypal'
        );

        $requestBody = json_encode(array(
            'intent' => 'authorize',
            'payer' => $this->payer,
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

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                $requestBody,
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));


        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->authorize(
            $this->accessToken,
            $this->total,
            $this->currency,
            $this->payer,
            $this->description
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('created', $payment->getState());
        $this->assertEquals('authorize', $payment->getIntent());
    }

    /**
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/
     */
    public function testAuthorizePaymentCreditCardMethod()
    {
        $status = 201;
        $json = '{"id":"PAY-22N76619X87876426KPQAJFA","create_time":"2014-08-04T22:09:24Z","update_time":"2014-08-04T22:09:41Z","state":"approved","intent":"authorize","payer":{"payment_method":"credit_card","funding_instruments":[{"credit_card":{"type":"visa","number":"xxxxxxxxxxxx0331","expire_month":"11","expire_year":"2018","first_name":"Betsy","last_name":"Buyer","billing_address":{"line1":"111 First Street","city":"Saratoga","state":"CA","postal_code":"95070","country_code":"US"}}}]},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","related_resources":[{"authorization":{"id":"9H822632XT878342A","create_time":"2014-08-04T22:09:24Z","update_time":"2014-08-04T22:09:41Z","amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"state":"authorized","parent_payment":"PAY-22N76619X87876426KPQAJFA","valid_until":"2014-09-02T22:09:24Z","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/9H822632XT878342A","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/9H822632XT878342A/capture","rel":"capture","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/9H822632XT878342A/void","rel":"void","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-22N76619X87876426KPQAJFA","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-22N76619X87876426KPQAJFA","rel":"self","method":"GET"}]}';

        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $this->payer = array(
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
        );

        $requestBody = json_encode(array(
            'intent' => 'authorize',
            'payer' => $this->payer,
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

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                $this->baseUrl.'/v1/payments/payment',
                array(
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Authorization' => $this->accessToken->getTokenType().' '.$this->accessToken->getAccessToken(),
                    'Content-Type' => 'application/json'
                ),
                $requestBody,
                array(
                    'debug' => $this->debug
                )
            )
            ->will($this->returnValue($this->expectedRequest));

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->expectedRequest)
            ->will($this->returnValue($this->expectedResponse));

        $service = new PaymentService(
            $this->client,
            $this->baseUrl,
            $this->returnUrl,
            $this->cancelUrl,
            $this->debug
        );
        $payment = $service->authorize(
            $this->accessToken,
            $this->total,
            $this->currency,
            $this->payer,
            $this->description
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals('approved', $payment->getState());
        $this->assertEquals('authorize', $payment->getIntent());
    }

    private function initAccessToken($type, $token)
    {
        $this->accessToken->expects($this->atLeastOnce())
            ->method('getTokenType')
            ->will($this->returnValue($type));

        $this->accessToken->expects($this->atLeastOnce())
            ->method('getAccessToken')
            ->will($this->returnValue($token));
    }

    private function initResponse($status, $json)
    {
        $this->expectedResponse->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($status));
        $this->expectedResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($json));
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
