<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;

class PaymentRequestBodyBuilder
{
    protected $intent;
    protected $payer;
    protected $urls;
    protected $transactions;

    public function __construct(
        PayerBuilder $payerBuilder = null,
        UrlsBuilder $urlsBuilder = null,
        TransactionsBuilder $transactionsBuilder = null
    ) {
        $this->payerBuilder = is_null($payerBuilder) ? new PayerBuilder() : $payerBuilder;
        $this->urlsBuilder = is_null($urlsBuilder) ? new UrlsBuilder() : $urlsBuilder;
        $this->transactionsBuilder = is_null($transactionsBuilder) ? new TransactionsBuilder() : $transactionsBuilder;
    }

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
