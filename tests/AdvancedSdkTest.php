<?php

namespace Paymongo\Tests;

use Paymongo\ApiResource;
use Paymongo\Entities\Error;
use Paymongo\Entities\Listing;
use Paymongo\Entities\Payment;
use Paymongo\Exceptions\InvalidRequestException;
use Paymongo\Exceptions\SignatureVerificationException;
use Paymongo\HttpClient;
use Paymongo\PaymongoClient;
use Paymongo\Services\PaymentService;
use Paymongo\Services\WebhookService;
use PHPUnit\Framework\TestCase;

/**
 * Contains more advanced tests for other critical SDK functionality.
 */
final class AdvancedSdkTest extends TestCase
{
    private PaymongoClient $client;

    protected function setUp(): void
    {
        $this->client = new PaymongoClient('sk_test_fake_api_key');
    }

    /**
     * CRUCIAL TEST 7: Does the `all()` method correctly handle a list response?
     */
    public function testServiceAllMethodReturnsListingObjectWithEntities(): void
    {
        // 1. Define a fake list response from the API
        $fakeResponse = new ApiResource([
            'data' => [
                ['id' => 'pay_aaa', 'type' => 'payment', 'attributes' => ['amount' => 100]],
                ['id' => 'pay_bbb', 'type' => 'payment', 'attributes' => ['amount' => 200]],
            ],
            'has_more' => true
        ]);

        // 2. Create and configure the mock HttpClient
        /** @var HttpClient&\PHPUnit\Framework\MockObject\MockObject $mockHttpClient */
        $mockHttpClient = $this->createMock(HttpClient::class);
        $mockHttpClient->method('request')->willReturn($fakeResponse);

        // 3. Call the service method
        $paymentService = new PaymentService($this->client, $mockHttpClient);
        $listing = $paymentService->all();

        // 4. Assert the response is parsed correctly
        $this->assertInstanceOf(Listing::class, $listing);
        $this->assertTrue($listing->has_more);
        $this->assertCount(2, $listing->data);
        $this->assertInstanceOf(Payment::class, $listing->data[0]);
        $this->assertSame('pay_aaa', $listing->data[0]->id);
        $this->assertSame(200, $listing->data[1]->amount);
    }

    /**
     * CRUCIAL TEST 8: Does webhook verification reject an expired timestamp?
     * This tests our replay attack protection.
     */
    public function testWebhookVerificationRejectsExpiredTimestamp(): void
    {
        $webhookService = new WebhookService($this->client);
        $secret = 'whsec_test_secret';
        $payload = '{"data":{}}';
        $expiredTimestamp = time() - 1000; // 1000 seconds ago, well outside our 5-min tolerance
        $signature = hash_hmac('sha256', "{$expiredTimestamp}.{$payload}", $secret);
        $signatureHeader = "t={$expiredTimestamp},live={$signature}";

        // Expect the specific exception for a timestamp failure
        $this->expectException(SignatureVerificationException::class);
        $this->expectExceptionMessage('Webhook timestamp exceeds tolerance.');

        $webhookService->constructEvent($payload, $signatureHeader, $secret);
    }

    /**
     * CRUCIAL TEST 9: Does the HttpClient throw an exception with correct, detailed info?
     * This tests the full error handling flow.
     */
    public function testApiExceptionContainsCorrectErrorDetails(): void
    {
        // 1. Define a fake, structured error response from the API
        $fakeErrorJson = [
            'errors' => [
                [
                    'code' => 'parameter_invalid',
                    'detail' => 'The amount you provided is invalid.',
                    'source' => ['pointer' => '/data/attributes/amount', 'attribute' => 'amount']
                ]
            ]
        ];
        $exceptionToThrow = new InvalidRequestException('Invalid request', $fakeErrorJson, 400);

        // 2. Configure the mock to throw our pre-built exception
        /** @var HttpClient&\PHPUnit\Framework\MockObject\MockObject $mockHttpClient */
        $mockHttpClient = $this->createMock(HttpClient::class);
        $mockHttpClient->method('request')->will($this->throwException($exceptionToThrow));

        $paymentService = new PaymentService($this->client, $mockHttpClient);

        try {
            // 3. Call the service, which should throw the exception
            $paymentService->retrieve('pay_123');
            $this->fail('An InvalidRequestException was not thrown.'); // Fails test if no exception is thrown
        } catch (InvalidRequestException $e) {
            // 4. Inspect the caught exception to ensure it contains the correct details
            $errors = $e->getErrors();
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Error::class, $errors[0]);
            $this->assertSame('parameter_invalid', $errors[0]->code);
            $this->assertTrue($errors[0]->hasSource());
            $this->assertSame('amount', $errors[0]->source->attribute);
        }
    }
}
