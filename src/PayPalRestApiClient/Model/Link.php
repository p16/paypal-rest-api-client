<?php

namespace PayPalRestApiClient\Model;

/**
 * The Link class represents a paypal HATEOAS link object
 */
class Link implements LinkInterface
{
    protected $href;
    protected $rel;
    protected $method;

    /**
     * Construct 
     *
     * @param string $href not null
     * @param string $rel not null
     * @param string $method not null
     *
     * @see https://developer.paypal.com/docs/api/#hateoas-links
     */
    public function __construct($href, $rel, $method)
    {
        $this->href = $href;
        $this->rel = $rel;
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}