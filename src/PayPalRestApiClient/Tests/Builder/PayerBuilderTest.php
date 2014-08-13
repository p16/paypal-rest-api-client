<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\PayerBuilder;

class PayerBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new PayerBuilder();
    }

    public function testBuild()
    {
        $funding_instruments = array(
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
        );
        $payer_info = array(
            'email' => 'exa@example.com'
        );
        $data = array(
            'payment_method' => 'credit_card',
            'funding_instruments' => $funding_instruments,
            'payer_info' => $payer_info,
        );

        $payer = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payer', $payer);
        $this->assertEquals('credit_card', $payer->getPaymentMethod());
        $this->assertEquals($funding_instruments, $payer->getFundingInstruments());
        $this->assertEquals($payer_info, $payer->getInfo());
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory keys missing for PayPalRestApiClient\Builder\PayerBuilder: payment_method
     */
    public function testBuildValidation()
    {
        $data = array();
        $payer = $this->builder->build($data);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Parameter payment_method not valid. Allowed values: "credit_card" and "paypal"
     */
    public function testBuildPaymentMethodValidation()
    {
        $data = array('payment_method' => 'example');
        $payer = $this->builder->build($data);
    }

    public function testBuildArrayExtraParameters()
    {
        $payer = array(
            'payment_method' => 'paypal_method',
            'funding_instruments' => array(),
            'exmple' => 'example',
        );
        $expected = array(
            'payment_method' => 'paypal_method',
            'funding_instruments' => array(),
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($payer)
        );
    }

    public function testBuildArrayFromObject()
    {
        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $payer->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue('paypal_method'));

        $expected = array(
            'payment_method' => 'paypal_method'
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($payer)
        );
    }

    public function testBuildArrayFromObjectWithFundingInstruments()
    {
        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $payer->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue('paypal_method'));
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

        $expected = array(
            'payment_method' => 'paypal_method',
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

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($payer)
        );
    }

    public function testBuildArrayFromArrayWithFundingInstruments()
    {
        $payer = $expected = array(
            'payment_method' => 'paypal_method',
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

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($payer)
        );
    }

    public function testBuildArrayFromArray()
    {
        $payer = $expected = array(
            'payment_method' => 'paypal_method'
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($payer)
        );
    }

    public function constructParametersProvider()
    {
        return array(
            array(null),
            array(array()),
        );
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @dataProvider constructParametersProvider
     */
    public function testBuildArrayValidation($payer)
    {
        $this->builder->buildArray($payer);
    }
}
