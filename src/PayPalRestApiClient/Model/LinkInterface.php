<?php

namespace PayPalRestApiClient\Model;

/**
 * The LinkInterface is the interface that any object that represents a paypal HATEOAS link should implement
 */
interface LinkInterface
{
    /**
     * @return string
     */
    public function getHref();

    /**
     * @return string
     */
    public function getRel();

    /**
     * @return string
     */
    public function getMethod();
}