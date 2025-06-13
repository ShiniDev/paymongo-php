***

# PayMongo PHP SDK: Usage Examples

This guide provides examples on how to use the modernized PayMongo PHP SDK. It is highly recommended to use [Composer](https://getcomposer.org/) to manage your project dependencies and for autoloading.

## 1. Initializing the Client

First, import the `PaymongoClient` class and instantiate it with your secret API key.

```php
<?php

require_once 'vendor/autoload.php';

use Paymongo\PaymongoClient;

// The constructor takes the API key as a direct string argument.
$client = new PaymongoClient('sk_test_YOUR_SECRET_KEY');

echo "Client initialized successfully!\n";
```

### Configuring Webhook Tolerance

You can set a custom tolerance (in seconds) for webhook signature verification. This is optional and defaults to 5 minutes (300 seconds).

```php
// Set a custom 10-minute (600 second) tolerance for all future webhook checks.
$client->setWebhookSignatureTolerance(600);

echo "Webhook tolerance is now set to {$client->getWebhookSignatureTolerance()} seconds.\n";
```

## 2. Handling Exceptions

The SDK uses a set of custom exceptions to handle API errors gracefully.

### AuthenticationException

This is thrown for `401 Unauthorized` errors, typically due to an invalid API key.

```php
<?php

use Paymongo\Exceptions\AuthenticationException;

try {
    $badClient = new PaymongoClient('sk_test_this_key_is_wrong');
    $badClient->payments->all();
} catch (AuthenticationException $e) {
    echo "Caught AuthenticationException: {$e->getMessage()}\n";
}
```

### InvalidRequestException

This is thrown for `400 Bad Request` errors, typically due to validation failures in your request payload. You can inspect the individual validation errors.

```php
<?php

use Paymongo\Exceptions\InvalidRequestException;

try {
    // This call will fail because 'amount' is missing from the attributes.
    $client->payments->create([
        'currency' => 'PHP',
        'description' => 'This payment will fail validation.',
        'source' => ['id' => 'src_123', 'type' => 'source']
    ]);
} catch (InvalidRequestException $e) {
    echo "Caught InvalidRequestException: {$e->getMessage()}\n";
    
    // Use getErrors() (plural) to loop through all validation errors.
    echo "Details:\n";
    foreach ($e->getErrors() as $error) {
        echo " - Code: {$error->code}, Detail: {$error->detail}, Source: {$error->source->attribute}\n";
    }
}
```

## 3. Basic API Usage

Access API resources like `payments` or `links` as properties on the `$client` object.

### Creating a Resource

```php
<?php

// Create a Payment Intent
$paymentIntent = $client->paymentIntents->create([
    'amount' => 10000, // 100.00 PHP
    'currency' => 'PHP',
    'payment_method_allowed' => ['card', 'paymaya'],
    'description' => 'Test Payment Intent'
]);

echo "Successfully created Payment Intent with ID: {$paymentIntent->id}\n";
```

### Retrieving a Resource

```php
<?php

// Retrieve the Payment Intent we just created
$retrievedIntent = $client->paymentIntents->retrieve($paymentIntent->id);

echo "Successfully retrieved Payment Intent. Status: {$retrievedIntent->status}\n";
// print_r($retrievedIntent); // To see the full object
```

## 4. Verifying Webhook Signatures

It is critical to verify the signature of incoming webhooks to ensure they are genuinely from PayMongo. This should be done in the script that acts as your webhook endpoint.

```php
<?php

use Paymongo\Exceptions\SignatureVerificationException;

// 1. Get the data from the HTTP request
$payload = file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
$webhookSecretKey = 'whsec_test_YOUR_WEBHOOK_SECRET_KEY';

// 2. Call constructEvent() to verify the signature and build the Event object
try {
    $event = $client->webhooks->constructEvent(
        $payload,
        $signatureHeader,
        $webhookSecretKey
    );

    echo "Webhook signature verified successfully!\n";
    echo "Event ID: {$event->id}\n";
    echo "Event Type: {$event->type}\n";

    // 3. Handle the event
    switch ($event->type) {
        case 'payment.succeeded':
            $payment = $event->resource; // The event resource is the payment object
            // Fulfill the order, send an email, etc.
            break;
        case 'payment.failed':
            // Notify the user, log the failure, etc.
            break;
        // ... handle other event types
        default:
            echo "Received unknown event type {$event->type}\n";
    }

    // Acknowledge receipt to PayMongo by sending a 200 OK status
    http_response_code(200);

} catch (SignatureVerificationException $e) {
    // This will catch invalid signatures, expired timestamps, or other errors.
    // Respond with a 400 Bad Request status to tell PayMongo not to retry.
    http_response_code(400);
    echo "Webhook verification failed: {$e->getMessage()}\n";
}
```