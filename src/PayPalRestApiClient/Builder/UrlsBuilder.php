<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;

/**
 * The UrlsBuilder builds arrays that represents paypal redirection urls
 */
class UrlsBuilder
{
    /**
     * Build an array that represents paypal redirection urls
     *
     * @param array|\ArrayAccess $urls an array that should have the following keys: return_url and cancel_url
     * 
     * @return array represents paypal redirection urls
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException if the input parameter is not an array or an object instance of \ArrayAccess, or the array keys "return_url" and "cancel_url" are not set
     * 
     * @see https://developer.paypal.com/docs/api/#redirecturls-object
     */
    public function buildArray($urls)
    {
        if (
            ($urls instanceof \ArrayAccess || is_array($urls)) &&
            (isset($urls['return_url']) && isset($urls['cancel_url']))
        ) {

            return array(
                'return_url' => $urls['return_url'],
                'cancel_url' => $urls['cancel_url']
            );
        }

        throw new BuilderException("urls array is not valid: return_url and cancel_url are mandatory");
    }
}
