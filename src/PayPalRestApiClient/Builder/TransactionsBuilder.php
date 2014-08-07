<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\TransactionInterface;

class TransactionsBuilder
{
    public function buildArray($transactions)
    {
        $this->assertTransactions($transactions);

        $transactionsData = array();
        foreach ($transactions as $transaction) {
            if ($transaction instanceof TransactionInterface) {
                $data = array(
                    'amount' => array(
                        'total' => $transaction->getAmount()->getTotal(),
                        'currency' => $transaction->getAmount()->getCurrency(),
                    ),
                    'description' => $transaction->getDescription()
                );

                if ($itemList = $transaction->getItemList())
                {
                    $data['item_list'] = $itemList;
                }

                $transactionsData[] = $data;
            }
            else {                
                $data = array(
                    'amount' => array(
                        'total' => $transaction['amount']['total'],
                        'currency' => $transaction['amount']['currency'],
                    ),
                    'description' => $transaction['description']
                );

                if (isset($transaction['item_list']) && ! empty($transaction['item_list']))
                {
                    $data['item_list'] = $transaction['item_list'];
                }

                $transactionsData[] = $data;
            }
        }

        return $transactionsData;
    }

    private function assertTransactions($transactions)
    {
        if (($transactions instanceof \ArrayAccess || is_array($transactions))) {

            foreach ($transactions as $transaction) {
                
                if ( ! $transaction instanceof TransactionInterface &&
                     ! ($transaction instanceof \ArrayAccess || is_array($transaction))
                ) {

                    throw new BuilderException("transactions is not valid: should contains object implementing TransactionInterface, ArrayAccess or array");
                }
            }

            return;
        }

        throw new BuilderException("transactions is not valid: should be an array or implement \ArrayAccess interface");
    }
}
