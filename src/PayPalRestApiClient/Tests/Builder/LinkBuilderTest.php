<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\LinkBuilder;

class LinkBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new LinkBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'href' => 'http://www.example.com',
            'rel' => "self",
            'method' => 'GET'
        );
        $link = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Link', $link);
        $this->assertEquals($data['href'], $link->getHref());
        $this->assertEquals($data['rel'], $link->getRel());
        $this->assertEquals($data['method'], $link->getMethod());
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory keys missing for PayPalRestApiClient\Builder\LinkBuilder: href, rel, method
     */
    public function testBuildValidation()
    {
        $data = array();
        $this->builder->build($data);
    }
}
