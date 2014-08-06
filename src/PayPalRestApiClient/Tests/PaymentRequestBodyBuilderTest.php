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
                array(array())
            ),
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

    public function testBuild()
    {
        $this->total = 15.00;
        $this->currency = 'EUR';
        $this->description = 'My fantastic transaction description';
        $this->returnUrl = 'http://example.com/success';
        $this->cancelUrl = 'http://example.com/cancel';
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


        $data = array(
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
        );

        if ( ! empty($this->items)) {
            $data['transactions'][0]['item_list']['items'] = $this->items;
        }

        if ( ! empty($this->shippingAddress)) {
            $data['transactions'][0]['item_list']['shipping_address'] = $this->shippingAddress;
        }
    }
}
