# Modernized PayMongo PHP SDK

[![Latest Version](https://img.shields.io/badge/version-v2.0.0-blue.svg)](https://github.com/YOUR_USERNAME/YOUR_REPO/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![PHP Version Require](https://img.shields.io/badge/php-%3E%3D%207.4-blue.svg)](https://www.php.net/)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg)]()

This project is a fork of the official PayMongo PHP SDK, modernized through a collaborative effort. The refactoring process was guided by shinidev and executed with the assistance of Google's Gemini AI to upgrade the codebase to modern PHP 7.4+ standards, improve security, and establish a robust testing foundation.

## Key Improvements in This Version

* ✅ **PHP 7.4+ Support**: The entire codebase now uses modern PHP features, including strict typing.
* ✅ **Critical Security Fixes**: Webhook signature verification is now protected against timing attacks (`hash_equals`) and replay attacks (timestamp verification).
* ✅ **Improved Developer Experience**: All classes and methods are strictly typed, providing better static analysis and autocompletion in modern IDEs.
* ✅ **Robust Error Handling**: The exception hierarchy has been rebuilt to be more logical and provide detailed error feedback from the API.
* ✅ **Testing Foundation**: A comprehensive test suite has been established to guarantee the reliability and correctness of the SDK.

## Requirements

* PHP >= 7.4
* cURL PHP Extension
* JSON PHP Extension
* Multibyte String (mbstring) PHP Extension

## Installation

The only supported installation method is via [Composer](https://getcomposer.org/).

```bash
composer require shinidev/paymongo-php
```

After installation, include Composer's autoloader in your project's startup script:
```php
<?php

require_once 'vendor/autoload.php';
```

> **Manual Installation (`initialize.php`) is no longer supported.**

## Usage

### 1. Initializing the Client

All interactions with the PayMongo API are done through the `PaymongoClient` object. You only need your secret API key to get started.

```php
<?php
use Paymongo\PaymongoClient;

$client = new PaymongoClient('sk_test_YOUR_SECRET_KEY');
```

### 2. Creating a Resource

Access API services (like `payments` or `links`) as properties on the client object. The SDK automatically handles wrapping the payload in the required `{"data": {"attributes": ...}}` structure.

```php
<?php
$paymentIntent = $client->paymentIntents->create([
    'amount' => 15000, // 150.00 PHP
    'currency' => 'PHP',
    'payment_method_allowed' => ['card', 'paymaya', 'gcash'],
    'description' => 'My Test Payment Intent'
]);

echo "Created Payment Intent: " . $paymentIntent->id;
```

### 3. Retrieving Resources

```php
<?php
// Retrieve a single resource by its ID
$retrievedIntent = $client->paymentIntents->retrieve($paymentIntent->id);
echo "Retrieved Status: " . $retrievedIntent->status;

// Retrieve a list of resources
$paymentsList = $client->payments->all(['limit' => 5]);
foreach ($paymentsList->data as $payment) {
    echo "Payment ID: " . $payment->id;
}
```

### 4. Handling API Errors

The SDK throws specific exceptions for different types of errors. You can use a `try...catch` block to handle them gracefully.

```php
<?php
use Paymongo\Exceptions\InvalidRequestException;
use Paymongo\Exceptions\AuthenticationException;
use Paymongo\Exceptions\ApiException;

try {
    // Attempt an API call that will fail
    $client->payments->create([ 'amount' => 50 ]); // Missing required fields

} catch (AuthenticationException $e) {
    // Handle bad API key
    echo "Authentication Error: " . $e->getMessage();

} catch (InvalidRequestException $e) {
    // Handle a validation error from the API
    echo "Invalid Request Error: " . $e->getMessage() . "\n";
    
    // Use getErrors() to see detailed validation messages
    foreach ($e->getErrors() as $error) {
        echo " - Field `{$error->source->attribute}`: {$error->detail}\n";
    }

} catch (ApiException $e) {
    // Handle any other generic API error
    echo "API Error: " . $e->getMessage();
}
```

### 5. Verifying Webhook Signatures

Verifying webhook signatures is critical for security. The `constructEvent` method provides a secure way to do this, including protection against replay attacks.

```php
<?php
use Paymongo\Exceptions\SignatureVerificationException;

// These values come from the HTTP request sent by PayMongo to your server
$payload = file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
$webhookSecretKey = 'whsec_test_YOUR_WEBHOOK_SECRET_KEY';

try {
    $event = $client->webhooks->constructEvent(
        $payload,
        $signatureHeader,
        $webhookSecretKey
    );

    echo "Webhook signature verified! Event type: {$event->type}\n";
    
    // Now you can safely handle the event
    switch ($event->type) {
        case 'payment.succeeded':
            $payment = $event->resource;
            // Fulfill the customer's order, send an email, etc.
            break;
        // ... handle other event types
    }

    // Respond to PayMongo with a 200 OK status to acknowledge receipt
    http_response_code(200);

} catch (SignatureVerificationException $e) {
    // The signature was invalid or the timestamp was too old. Do not trust the payload.
    // Respond with a 400 Bad Request status to tell PayMongo not to retry.
    http_response_code(400);
    echo "Webhook verification failed: {$e->getMessage()}\n";
}
```

## Official API Documentation

This SDK is a wrapper that provides convenient access to the PayMongo REST API. For comprehensive details on API endpoints, request parameters, response structures, and general API behavior, please always refer to the official **[PayMongo Developer Documentation](https://developers.paymongo.com/)**.


## Development and Testing

To run the test suite for this project:

1.  Clone the repository.
2.  Run `composer install`.
3.  Run the tests from the project root:
    ```bash
    ./vendor/bin/phpunit
    ```

## License

This project is a fork of the original paymongo/paymongo-php library, which is copyrighted by PayMongo, Inc. and licensed under the MIT license. Modifications and new work in this repository are copyrighted by shinidev (C) 2025 and are also released under the MIT License.
