<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\AuthorizationBuilder;

class AuthorizationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new AuthorizationBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'id' => '6JK78052MJ7446007',
            'create_time' => '2014-08-08T17:11:00Z',
            'update_time' => '2014-08-08T17:11:19Z',
            'amount' => array(
                'total' => '12.35',
                'currency' => 'EUR',
                'details' => array(
                    'subtotal' => '12.35'
                )
            ),
            'state' => 'authorized',
            'parent_payment' => 'PAY-2LS84841MB1756502KPSQJJA',
            'valid_until' => '2014-09-06T17:11:00Z',
            'links' => array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007',
                    'rel' => 'self',
                    'method' => 'GET',
                ),
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/capture',
                    'rel' => 'capture',
                    'method' => 'POST',
                ),
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/void',
                    'rel' => 'void',
                    'method' => 'POST',
                ),
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-2LS84841MB1756502KPSQJJA',
                    'rel' => 'parent_payment',
                    'method' => 'GET',
                )
            )
        );
        $authorization = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Authorization', $authorization);
        $this->assertEquals('6JK78052MJ7446007', $authorization->getId());
        $this->assertEquals('2014-08-08T17:11:00Z', $authorization->getCreateTime());
        $this->assertEquals('2014-08-08T17:11:19Z', $authorization->getUpdateTime());
        $this->assertEquals('authorized', $authorization->getState());
        $this->assertEquals('PAY-2LS84841MB1756502KPSQJJA', $authorization->getParentPayment());
        $this->assertEquals('2014-09-06T17:11:00Z', $authorization->getValidUntil());
        
        $this->assertInstanceOf('PayPalRestApiClient\Model\Amount', $authorization->getAmount());
        $this->assertEquals('12.35', $authorization->getAmount()->getTotal());
        $this->assertEquals('EUR', $authorization->getAmount()->getCurrency());
        $this->assertEquals(array('subtotal' => '12.35'), $authorization->getAmount()->getDetails());

        $this->assertCount(4, $authorization->getLinks());
        $this->assertTrue(is_array($authorization->getLinks()));
        
        $this->assertInstanceOf('PayPalRestApiClient\Model\Link', $authorization->getLinks()[0]);
        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007',
            $authorization->getLinks()[0]->getHref()
        );
        $this->assertEquals('self', $authorization->getLinks()[0]->getRel());
        $this->assertEquals('GET', $authorization->getLinks()[0]->getMethod());
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory keys missing for PayPalRestApiClient\Builder\AuthorizationBuilder: amount, create_time, update_time, state, parent_payment, id, valid_until, links
     */
    public function testBuildValidationNoScope()
    {
        $data = array();
        $authorization = $this->builder->build($data);
    }
}
