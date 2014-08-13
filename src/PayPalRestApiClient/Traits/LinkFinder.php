<?php

namespace PayPalRestApiClient\Traits;

use PayPalRestApiClient\Model\LinkInterface;

/**
 * LinkFinder trait defines how to search in a list of PayPalRestApiClient\Model\LinkInterface instances
 */
trait LinkFinder
{
    /**
     * @return array of PayPalRestApiClient\Model\LinkInterface instances
     */
    abstract public function getLinks();

    /**
     * Searches and returns the first link object with attribute "rel" equal to $rel input variable
     * 
     * @param string $rel
     *
     * @return PayPalRestApiClient\Model\LinkInterface
     */
    protected function findLink($rel)
    {
        $result = array_filter($this->getLinks(), function($link) use ($rel) {
            if ( ! $link instanceof LinkInterface) {
                throw new \RuntimeException("All element of links array should implement PayPalRestApiClient\Model\LinkInterface");
            }

            if ($rel === $link->getRel()) {
                return true;
            }
        });

        if (count($result) > 0) {
            $link = array_shift($result);

            return $link;
        }

        throw new \RuntimeException("Cannot find a link corresponding to rel '".$rel."'");
    }
}