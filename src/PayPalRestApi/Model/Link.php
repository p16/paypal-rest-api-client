<?php

namespace PayPalRestApi\Model;

class Link
{
    protected $href;
    protected $rel;
    protected $method;

    /**
     * @see https://developer.paypal.com/docs/api/#hateoas-links
     */
    public function __construct($href, $rel, $method)
    {
        $this->href = $href;
        $this->rel = $rel;
        $this->method = $method;
    }

    public function getHref()
    {
        return $this->href;
    }

    public function getRel()
    {
        return $this->rel;
    }

    public function getMethod()
    {
        return $this->method;
    }
}