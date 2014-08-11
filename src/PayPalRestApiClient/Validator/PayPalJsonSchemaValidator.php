<?php

namespace PayPalRestApiClient\Validator;

class PayPalJsonSchemaValidator
{
    protected $schema;
    protected $data;
    protected $refResolverPath;
    protected $validator;

    public function __construct($schema, $data)
    {
        $this->schema = 'file://'.realpath(__DIR__.'/schema/'.$schema);
        $this->data = $data;
        $this->refResolverPath = 'file://'.__DIR__.'/schema/';
    }

    public function isValid()
    {
        $retriever = new \JsonSchema\Uri\UriRetriever;
        $schema = $retriever->retrieve($this->schema);

        $refResolver = new \JsonSchema\RefResolver($retriever);
        $refResolver->resolve($schema, $this->refResolverPath);

        $this->validator = new \JsonSchema\Validator();
        $this->validator->check($this->data, $schema);

        return $this->validator->isValid();
    }

    public function getErrors()
    {
        return $this->validator->getErrors();
    }
}
