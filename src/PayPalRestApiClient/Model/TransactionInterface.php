<?php

namespace PayPalRestApiClient\Model;

/**
 * The TransactionInterface is the interface that any object that represents a paypal transaction should implement
 */
interface TransactionInterface
{
    /**
     * @return PayPalRestApiClient\Model\AmountInterface
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return array
     */
    public function getItemList();

    /**
     * @return array
     */
    public function getRelatedResources();
}