<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\TransactionInterface;

/**
 * The TransactionsBuilder builds arrays that represents a paypal transactions array or an array of Transaction onjects given an array
 */
class TransactionsBuilder extends AbstractBuilder
{
    protected $amountBuilder;
    protected $authorizationBuilder;

    public function __construct()
    {
        $this->amountBuilder = new AmountBuilder();
        $this->authorizationBuilder = new AuthorizationBuilder();
    }

    public function setAmountBuilder($amountBuilder)
    {
        $this->amountBuilder = $amountBuilder;
    }

    public function setAuthorizationBuilder($authorizationBuilder)
    {
        $this->authorizationBuilder = $authorizationBuilder;
    }

    /**
     * Build an array of PayPalRestApiClient\Model\Transaction instances given an array of arrays
     *
     * @param array $transactions Should contains arrays with at least the amount key set
     * 
     * @return array of PayPalRestApiClient\Model\Transaction instances
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set for each transaction
     *
     * @see https://developer.paypal.com/docs/api/#transaction-object
     */
    public function build(array $transactions)
    {
        $results = array();
        foreach ($transactions as $transaction) {
            $results[] = $this->buildTransaction($transaction);
        }

        return $results;
    }

    protected function buildTransaction(array $data)
    {
        $this->validateArrayKeys(
            array('amount'),
            $data
        );

        $amount = $this->amountBuilder->build($data['amount']);

        $description = null;
        if (isset($data['description'])) {
            $description = $data['description'];
        }

        $itemList = array();
        if (isset($data['item_list'])) {
            $itemList = $data['item_list'];
        }

        $relatedResources = array();
        if (isset($data['related_resources'])) {
            $relatedResources = $this->buildRelatedResources($data['related_resources']);
        }

        return new Transaction(
            $amount,
            $description,
            $itemList,
            $relatedResources
        );

    }

    private function buildRelatedResources($relatedResources)
    {
        $results = array();
        foreach ($relatedResources as $index => $resourceItems) {
            $results[$index] = array();
            foreach ($resourceItems as $key => $resource) {

                switch ($key) {
                    case 'authorization':
                        
                        $results[$index][$key] = $this->authorizationBuilder->build($resource);
                        break;
                    
                    default:
                        $results[$index][$key] = $resource;
                        break;
                }
            }
        }

        return $results;
    }

    /**
     * Build an array that represents a paypal transactions array
     *
     * @param array|\ArrayAccess $transactions an array of arrays or instances of PayPalRestApiClient\Model\TransactionInterface od \ArrayAccess
     * 
     * @return array represents an array of transaction
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException if at least one transaction is not an array, or an object implementing \ArrayAccess interface or PayPalRestApiClient\Model\TransactionInterface
     * 
     * @see https://developer.paypal.com/docs/api/#transaction-object
     */
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
