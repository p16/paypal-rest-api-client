<?php

namespace PayPalRestApiClient\Model;

interface TransactionInterface
{
    public function getAmount();

    public function getDescription();

    public function getItemList();

    public function getRelatedResources();
}