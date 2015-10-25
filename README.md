PHP PayPal REST API Client
==========================

[![Build Status](https://travis-ci.org/p16/paypal-rest-api-client.svg?branch=master)](https://travis-ci.org/p16/paypal-rest-api-client) [![Latest Stable Version](https://poser.pugx.org/p16/paypal-rest-api-client/v/stable.svg)](https://packagist.org/packages/p16/paypal-rest-api-client) [![Total Downloads](https://poser.pugx.org/p16/paypal-rest-api-client/downloads.svg)](https://packagist.org/packages/p16/paypal-rest-api-client) [![Latest Unstable Version](https://poser.pugx.org/p16/paypal-rest-api-client/v/unstable.svg)](https://packagist.org/packages/p16/paypal-rest-api-client) [![License](https://poser.pugx.org/p16/paypal-rest-api-client/license.svg)](https://packagist.org/packages/p16/paypal-rest-api-client)

This library wants to be a PHP client for the [PayPal REST API](https://developer.paypal.com/docs/api/).

There is an official SDK and you can find it [here](https://github.com/paypal/rest-api-sdk-php).

I tried it and I tried to [contribute on it](https://github.com/p16/rest-api-sdk-php) (the tests were calling the actual paypal sandbox). 
But I needed a simpler and OOP library, and I did not have the time to contribute as much as I think is needed on the official one, therefore I did this one.

I'm working on a first stable realease that should be out before August 15th.

Feedback, PR and contribution are welcome.



Features
--------

At the moment the only [PayPal REST API](https://developer.paypal.com/docs/api/) calls implemented are:

- Require an access token: https://developer.paypal.com/docs/api/#authentication--headers
- Create a payment (only with "paypal" and "credit_card" payment methods): https://developer.paypal.com/docs/api/#create-a-payment 
- Execute a payment: https://developer.paypal.com/docs/api/#execute-an-approved-paypal-payment
- Authorize and capture a payment: https://developer.paypal.com/docs/integration/direct/capture-payment/
- Validation of json schema for payment request body: [Json Schema](http://json-schema.org/), [PHP Json Schema validator](https://github.com/justinrainbow/json-schema), [PayPal REST API objects](https://developer.paypal.com/docs/api/)

Installation
------------

Add the following dependency to your composer.json


    "p16/paypal-rest-api-client": "dev-master"


Run

    composer update p16/paypal-rest-api-client



Running tests
-------------

Donwload the repository

Run

    composer install


From the root folder run

    phpunit -c .


Documentation
-------------

**Using the library**

- [Direct payment with paypal method](examples/directPaymentPaypalMethod.md)
- [Direct payment with credit card method](examples/directPaymentCreditCardMethod.md)
- [Payment authorization and capture with paypal method](examples/paymentAuthorizationAndCapturePaypalMethod.md)
- [Payment authorization and capture with credit card method](examples/paymentAuthorizationAndCaptureCreditCardMethod.md)


**PayPal Json schema validation**

A [Json Schema validator](https://github.com/justinrainbow/json-schema) is used to validate a call request body. You can find all the available schema definition [here](src/PayPalRestApiClient/Validator/schema).


**TO-DO for release 0.2**

- Move PayPalJsonSchemaValidator into a standalone repo (?)

- Make the builder aware of the PayPalJsonSchemaValidator, so that they can validate what they are building when building from/to json

- Add strict validation to model classes (?)

**TO-DO for release 0.3**

- Add ["billing plans"](https://developer.paypal.com/docs/api/#billing-plans-and-agreements) feature 

License
-------

Licensed under the [MIT license](http://opensource.org/licenses/MIT)

Read [LICENSE](LICENSE) for more information


