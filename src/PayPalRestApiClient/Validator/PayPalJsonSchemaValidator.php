<?php

namespace PayPalRestApiClient\Validator;

/**
 * PayPalJsonSchemaValidator defines a validator for json strings against paypal json schema
 */
class PayPalJsonSchemaValidator
{
    protected $schema;
    protected $data;
    protected $refResolverPath;
    protected $validator;

    /**
     * Construct
     *
     * @param string $schema represents a specifica schema to validate against (ex.: 'payment.json')
     * @param mixed $data json decoded string (with "assoc" parameter set to false http://it1.php.net/json_decode )
     */
    public function __construct($schema, $data)
    {
        $this->schema = 'file://'.realpath(__DIR__.'/schema/'.$schema);
        $this->data = $data;
        $this->refResolverPath = 'file://'.__DIR__.'/schema/';
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        $retriever = new \JsonSchema\Uri\UriRetriever;
        $schema = $retriever->retrieve($this->schema);

        $refResolver = new \JsonSchema\RefResolver($retriever);
        // @see https://github.com/justinrainbow/json-schema/issues/180
        $refResolver::$maxDepth = 20;
        $refResolver->resolve($schema, $this->refResolverPath);

        $this->validator = new \JsonSchema\Validator();
        $this->validator->check($this->data, $schema);

        return $this->validator->isValid();
    }

    /**
     * @return array errors array of arrays, with "property" and "message" keys for each errors
     */
    public function getErrors()
    {
        return $this->validator->getErrors();
    }
}
