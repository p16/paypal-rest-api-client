<?php

namespace PayPalRestApiClient\Traits;

use PayPalRestApiClient\Exception\BuilderException;

/**
 * AssertArrayOfObject trait defines how to assert that an array as object element, instances of a given calss
 */
trait AssertArrayOfObject
{
    /**
     * Checks if an array contains only instances of a given class 
     *
     * @param array $objects
     * @param string $class
     * @param string $errorLabel a name that identifies the object array (products, links, etc.)
     */
    protected function assertArrayOfObjects(array $objects, $class, $errorLabel = 'objects')
    {
        foreach ($objects as $object) {
            if ( ! $object instanceof $class) {

                throw new BuilderException($errorLabel.' should be an array of object extending/implementing '.$class);
            }
        }
    }
}