<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;

/**
 * The AbstractBuilder class contains common methods for model objects builders 
 */
abstract class AbstractBuilder
{
    abstract public function build(array $data);

    /**
     * Given an array of values $mandatoryKeys checks if the $data array contains all the keys specified in $mandatoryKeys
     *
     * @param array $mandatoryKeys
     * @param array $data
     * @param string $label
     *
     * @return void
     * @throws PayPalRestApiClient\Exception\BuilderException
     */
    protected function validateArrayKeys(array $mandatoryKeys, $data, $label = null)
    {
        $label = get_class($this);

        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory keys missing for '.$label.': '.implode(', ', $diff));
        }
    }
}
