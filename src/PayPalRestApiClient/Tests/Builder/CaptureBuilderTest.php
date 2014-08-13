<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\CaptureBuilder;

class CaptureBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new CaptureBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'id' => 'PAY-ID',
            'create_time' => "2014-08-08T17:11:00Z",
            'update_time' => "2014-08-08T17:11:00Z",
            'amount' => array(
                'total' => 12.00,
                'currency' => 'EUR'
            ),
            'is_final_capture' => true,
            'state' => 'copmpleted',
            'parent_payment' => 'PAY-ID-ID',
            'links' => array(
                array(
                    'href' => 'https://link',
                    'rel' => 'self',
                    'method' => 'GET',
                )
            )
        );
        $capture = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Capture', $capture);
        $this->assertEquals('PAY-ID', $capture->getId());
        $this->assertEquals('2014-08-08T17:11:00Z', $capture->getCreateTime());
        $this->assertEquals('2014-08-08T17:11:00Z', $capture->getUpdateTime());

        $this->assertInstanceOf('PayPalRestApiClient\Model\Amount', $capture->getAmount());
        $this->assertEquals('12.00', $capture->getAmount()->getTotal());
        $this->assertEquals('EUR', $capture->getAmount()->getCurrency());
        $this->assertEquals(array(), $capture->getAmount()->getDetails());

        $this->assertEquals(true, $capture->isFinalCapture());
        $this->assertEquals('copmpleted', $capture->getState());
        $this->assertEquals('PAY-ID-ID', $capture->getParentPayment());
        $this->assertTrue(is_array($capture->getLinks()));
        
        $this->assertInstanceOf('PayPalRestApiClient\Model\Link', $capture->getLinks()[0]);
        $this->assertEquals('https://link', $capture->getLinks()[0]->getHref());
        $this->assertEquals('self', $capture->getLinks()[0]->getRel());
        $this->assertEquals('GET', $capture->getLinks()[0]->getMethod());
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory keys missing for PayPalRestApiClient\Builder\CaptureBuilder: id, create_time, update_time, amount, is_final_capture, state, parent_payment, links
     */
    public function testBuildValidationNoScope()
    {
        $data = array();
        $authorization = $this->builder->build($data);
    }
}
