<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;

class UrlsBuilder
{
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
