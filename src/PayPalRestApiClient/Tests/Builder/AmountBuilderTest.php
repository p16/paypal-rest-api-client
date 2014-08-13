<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\AmountBuilder;

class AmountBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new AmountBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'total' => '12.00',
            'currency' => "EUR",
            'details' => array(
                'subtotal' => 12.00
            )
        );
        $amount = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Amount', $amount);
        $this->assertEquals('12.00', $amount->getTotal());
        $this->assertEquals('EUR', $amount->getCurrency());
        $this->assertEquals(array('subtotal' => 12.00), $amount->getDetails());
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory keys missing for PayPalRestApiClient\Builder\AmountBuilder: currency, total
     */
    public function testBuildValidation()
    {
        $data = array();
        $this->builder->build($data);
    }
}
