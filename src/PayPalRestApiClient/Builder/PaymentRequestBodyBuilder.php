<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;

/**
 * The PaymentRequestBodyBuilder builds a representation of a payment request body
 */
class PaymentRequestBodyBuilder
{
    protected $intent;
    protected $payer;
    protected $urls;
    protected $transactions;

    /**
     * Constructor
     *
     * @param $payerBuilder PayPalRestApiClient\Builder\PayerBuilder or null
     * @param $urlsBuilder PayPalRestApiClient\Builder\UrlsBuilder or null
     * @param $transactionsBuilder PayPalRestApiClient\Builder\TransactionsBuilder or null
     */
    public function __construct(
        PayerBuilder $payerBuilder = null,
        UrlsBuilder $urlsBuilder = null,
        TransactionsBuilder $transactionsBuilder = null
    ) {
        $this->payerBuilder = is_null($payerBuilder) ? new PayerBuilder() : $payerBuilder;
        $this->urlsBuilder = is_null($urlsBuilder) ? new UrlsBuilder() : $urlsBuilder;
        $this->transactionsBuilder = is_null($transactionsBuilder) ? new TransactionsBuilder() : $transactionsBuilder;
    }

    /**
     * Build an array or a json string that represents the body of a payment request
     *
     * @param string $intent not null and should be one of the following values: sale, authorize, order
     * @param array|\ArrayAccess|PayPalRestApiClient\Model\PayerInterface $payer checkout the PayerBuilder documentation
     * @param array|\ArrayAccess $urls checkout the UrlsBuilder documentation
     * @param array|\ArrayAccess|PayPalRestApiClient\Model\TransactionInterface $transactions
     * @param $returnArray boolean default false
     * 
     * @return array|json return by default a json string, if $returnArray is set to true it will return an array
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException if intent is not valid or the other arrays will not pass the validation on the other builders
     *
     * @see https://developer.paypal.com/docs/api/#payments
     */
    public function build($intent, $payer, $urls, $transactions, $returnArray = false)
    {
        $this->assertIntent($intent);
        $this->intent = $intent;

        $requestBody = array();
        $requestBody['intent'] = $this->intent;
        $requestBody['payer'] = $this->payerBuilder->buildArray($payer);
        $requestBody['transactions'] = $this->transactionsBuilder->buildArray($transactions);
        $requestBody['redirect_urls'] = $this->urlsBuilder->buildArray($urls);

        if ($returnArray) {
            return $requestBody;
        }

        return json_encode($requestBody);
    }

    private function assertIntent($intent)
    {
        if ( ! in_array($intent, array('sale', 'authorize', 'order'))) {
            
            throw new BuilderException("intent is not valid: allowed value are 'sale', 'authorize', 'order'");
        }
    }

}
