<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;

class PaymentRequestBodyBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function constructParametersProvider()
    {
        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');

        return array(
            array(null, null, null, null),
            array('authorize', null, null, null),
            array('authorize', $payer, null, null),
            array('authorize', array('payment_method' => 'paypal'), null, null),
            array('authorize', array('payment_method' => 'paypal'), array(), null),
            array('authorize', array('payment_method' => 'paypal'), array('url' => 'url'), null),
            array(
                'authorize',
                array('payment_method' => 'paypal'),
                array('return_url' => 'url', 'cancel_url' => 'url'),
                null
            ),
            array(
                'authorize',
                array('payment_method' => 'paypal'),
                array('return_url' => 'url', 'cancel_url' => 'url'),
                array(new \stdClass())
            )
        );
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @dataProvider constructPArametersProvider
     */
    public function testConstructValidations($intent, $payer, $urls, $transactions)
    {
        $builder = new PaymentRequestBodyBuilder($intent, $payer, $urls, $transactions);
    }

    public function testConstruct()
    {
        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');

        $builder = new PaymentRequestBodyBuilder(
            'authorize',
            $payer,
            array('return_url' => 'url', 'cancel_url' => 'url'),
            array($transaction)
        );

        $this->assertInstanceOf('PayPalRestApiClient\Builder\PaymentRequestBodyBuilder', $builder);

        $builder = new PaymentRequestBodyBuilder(
            'authorize',
            array('payment_method' => 'paypal'),
            array('return_url' => 'url', 'cancel_url' => 'url'),
            array($transaction)
        );

        $this->assertInstanceOf('PayPalRestApiClient\Builder\PaymentRequestBodyBuilder', $builder);
    }

    public function testBuildWhenPayerAndAmountAreObjet()
    {
        $amount = $this->getMock('PayPalRestApiClient\Model\AmountInterface');
        $amount->expects($this->once())
            ->method('getTotal')
            ->will($this->returnValue('15.00'));
        $amount->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue('EUR'));

        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $payer->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue('paypal_method'));

        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transaction->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue($amount));
        $transaction->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue('My description'));

        $expectedData = array(
            'intent' => 'authorize',
            'payer' => array(
                'payment_method' => 'paypal_method'
            ),
            'redirect_urls' => array(
                'return_url' => 'url',
                'cancel_url' => 'url'
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => '15.00',
                        'currency' => 'EUR',
                    ),
                    'description' => 'My description'
                )
            ),
        );

        $builder = new PaymentRequestBodyBuilder(
            'authorize',
            $payer,
            array('return_url' => 'url', 'cancel_url' => 'url'),
            array($transaction)
        );

        $data = $builder->build();

        $this->assertEquals($expectedData, $data);
    }

    public function testBuildWhenPayerAndAmountAreArray()
    {
        $payer = array(
            'payment_method' => 'paypal_method'
        );

        $transactions = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $expectedData = array(
            'intent' => 'authorize',
            'payer' => array(
                'payment_method' => 'paypal_method'
            ),
            'redirect_urls' => array(
                'return_url' => 'url',
                'cancel_url' => 'url'
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => '15.00',
                        'currency' => 'EUR',
                    ),
                    'description' => 'My description'
                )
            ),
        );

        $builder = new PaymentRequestBodyBuilder(
            'authorize',
            $payer,
            array('return_url' => 'url', 'cancel_url' => 'url'),
            $transactions
        );

        $data = $builder->build();

        $this->assertEquals($expectedData, $data);
    }

    public function testBuildWhenCreditCardIsObject()
    {
        $amount = $this->getMock('PayPalRestApiClient\Model\AmountInterface');
        $amount->expects($this->once())
            ->method('getTotal')
            ->will($this->returnValue('15.00'));
        $amount->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue('EUR'));

        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $payer->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue('credit_card'));
        $payer->expects($this->once())
            ->method('getFundingInstruments')
            ->will($this->returnValue(
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
            ));

        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transaction->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue($amount));
        $transaction->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue('My description'));
        $transaction->expects($this->once())
            ->method('getItemList')
            ->will($this->returnValue(
                array(
                    'items' => array(
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
                    ),
                    'shipping_address' => array(
                        'recipient_name' => 'Fi Fi',
                        'type' => 'residential',
                        'line1' => 'Via del mare',
                        'line2' => '',
                        'city' => 'Milano',
                        'country_code' => 'IT',
                        'postal_code' => '60010',
                        'state' => '',
                        'phone' => '3213213211',
                    )
                )
            ));

        $expectedData = array(
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
                )
            ),
            'redirect_urls' => array(
                'return_url' => 'url',
                'cancel_url' => 'url'
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => '15.00',
                        'currency' => 'EUR',
                    ),
                    'description' => 'My description',
                    'item_list' => array(
                        'items' => array(
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
                        ),
                        'shipping_address' => array(
                            'recipient_name' => 'Fi Fi',
                            'type' => 'residential',
                            'line1' => 'Via del mare',
                            'line2' => '',
                            'city' => 'Milano',
                            'country_code' => 'IT',
                            'postal_code' => '60010',
                            'state' => '',
                            'phone' => '3213213211',
                        )
                    )
                )
            ),
        );

        $builder = new PaymentRequestBodyBuilder(
            'authorize',
            $payer,
            array('return_url' => 'url', 'cancel_url' => 'url'),
            array($transaction)
        );

        $data = $builder->build();

        $this->assertEquals($expectedData, $data);
    }

    public function testBuildWhenCreditCardIsarray()
    {
        $payer = array(
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
            )
        );

        $transactions = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description',
                'item_list' => array(
                    'items' => array(
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
                    ),
                    'shipping_address' => array(
                        'recipient_name' => 'Fi Fi',
                        'type' => 'residential',
                        'line1' => 'Via del mare',
                        'line2' => '',
                        'city' => 'Milano',
                        'country_code' => 'IT',
                        'postal_code' => '60010',
                        'state' => '',
                        'phone' => '3213213211',
                    )
                )
            )
        );

        $expectedData = array(
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
                )
            ),
            'redirect_urls' => array(
                'return_url' => 'url',
                'cancel_url' => 'url'
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => '15.00',
                        'currency' => 'EUR',
                    ),
                    'description' => 'My description',
                    'item_list' => array(
                        'items' => array(
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
                        ),
                        'shipping_address' => array(
                            'recipient_name' => 'Fi Fi',
                            'type' => 'residential',
                            'line1' => 'Via del mare',
                            'line2' => '',
                            'city' => 'Milano',
                            'country_code' => 'IT',
                            'postal_code' => '60010',
                            'state' => '',
                            'phone' => '3213213211',
                        )
                    )
                )
            ),
        );

        $builder = new PaymentRequestBodyBuilder(
            'authorize',
            $payer,
            array('return_url' => 'url', 'cancel_url' => 'url'),
            $transactions
        );

        $data = $builder->build();

        $this->assertEquals($expectedData, $data);
    }
}
