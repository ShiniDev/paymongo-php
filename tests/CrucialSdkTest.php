<?php

namespace Paymongo\Tests;

// Import all the necessary classes we will be testing or using.
use Paymongo\ApiResource;
use Paymongo\Entities\Event;
use Paymongo\Entities\Payment;
use Paymongo\Exceptions\InvalidRequestException;
use Paymongo\Exceptions\SignatureVerificationException;
use Paymongo\HttpClient;
use Paymongo\PaymongoClient;
use Paymongo\Services\PaymentService;
use Paymongo\Services\WebhookService;
use PHPUnit\Framework\TestCase;

/**
 * A single test file that covers the most critical functionality of the SDK.
 */
final class CrucialSdkTest extends TestCase
{
    private PaymongoClient $client;

    // This method is run before each test.
    protected function setUp(): void
    {
        $this->client = new PaymongoClient('sk_test_fake_api_key');
    }

    /**
     * CRUCIAL TEST 1: Can the client be created and can we access services?
     * This is the most basic "smoke test" to ensure the SDK starts up.
     */
    public function testClientCanBeInstantiatedAndServicesAccessed(): void
    {
        $this->assertInstanceOf(PaymongoClient::class, $this->client);
        $this->assertInstanceOf(PaymentService::class, $this->client->payments);
    }

    /**
     * CRUCIAL TEST 2: Can a service correctly parse a successful API response?
     * This tests the 'read' path and ensures entities are created correctly.
     */
    public function testServiceRetrieveMethodWorksWithMockedHttp(): void
    {
        // 1. Define the fake API response
        $fakeResponse = new ApiResource([
            'data' => [
                'id' => 'pay_123',
                'type' => 'payment',
                'attributes' => ['amount' => 10000, 'status' => 'succeeded'],
            ]
        ]);

        // 2. Create a mock HttpClient that will return our fake response
        /** @var HttpClient&\PHPUnit\Framework\MockObject\MockObject $mockHttpClient */
        $mockHttpClient = $this->createMock(HttpClient::class);
        $mockHttpClient->method('request')->willReturn($fakeResponse);

        // 3. Inject the mock into the service and call the method
        $paymentService = new PaymentService($this->client, $mockHttpClient);
        $payment = $paymentService->retrieve('pay_123');

        // 4. Assert the returned Payment object has the correct data
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame('pay_123', $payment->id);
        $this->assertSame(10000, $payment->amount);
    }

    /**
     * CRUCIAL TEST 3: Does a service send the correct data when creating a resource?
     * This tests the 'write' path.
     */
    public function testServiceCreateMethodSendsCorrectDataToHttp(): void
    {
        /** @var HttpClient&\PHPUnit\Framework\MockObject\MockObject $mockHttpClient */
        $mockHttpClient = $this->createMock(HttpClient::class);

        // We need to check the ARGUMENTS sent to the `request` method.
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($opts) {
                // Assert that the 'params' being sent are correct
                $this->assertSame('POST', $opts['method']);
                $this->assertArrayHasKey('amount', $opts['params']);
                $this->assertSame(50000, $opts['params']['amount']);
                return true; // Return true to indicate the arguments are valid
            }))
            ->willReturn(new ApiResource(['data' => ['id' => 'pay_abc']])); // Still need to return a valid resource

        $paymentService = new PaymentService($this->client, $mockHttpClient);
        $paymentService->create(['amount' => 50000, 'currency' => 'PHP', 'description' => 'Test']);
    }

    /**
     * CRUCIAL TEST 4: Does the webhook validator accept a VALID signature?
     * This is the most important security test.
     */
    public function testWebhookVerificationHandlesValidSignature(): void
    {
        $webhookService = new WebhookService($this->client);
        $secret = 'whsec_test_secret';
        $payload = '{"data":{"id":"evt_123","type":"event"}}';
        $timestamp = time();
        $expectedSignature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);
        $signatureHeader = "t={$timestamp},live={$expectedSignature}";

        // Call the method and assert it returns an Event object
        $event = $webhookService->constructEvent($payload, $signatureHeader, $secret);
        $this->assertInstanceOf(Event::class, $event);
    }

    /**
     * CRUCIAL TEST 5: Does the webhook validator REJECT an INVALID signature?
     * This is the other half of the security test.
     */
    public function testWebhookVerificationRejectsInvalidSignature(): void
    {
        // Expect a specific exception to be thrown
        $this->expectException(SignatureVerificationException::class);

        $webhookService = new WebhookService($this->client);
        $secret = 'whsec_test_secret';
        $payload = '{"data":{"id":"evt_123","type":"event"}}';
        $timestamp = time();
        $signatureHeader = "t={$timestamp},live=invalid_signature"; // Use a bad signature

        // This call should throw the exception, and the test will pass.
        $webhookService->constructEvent($payload, $signatureHeader, $secret);
    }

    /**
     * CRUCIAL TEST 6: Does a service correctly throw an exception from HttpClient?
     * This tests the error handling path.
     */
    public function testServiceThrowsExceptionFromHttp(): void
    {
        // 1. Tell the mock HttpClient to THROW an exception when called.
        /** @var HttpClient&\PHPUnit\Framework\MockObject\MockObject $mockHttpClient */
        $mockHttpClient = $this->createMock(HttpClient::class);
        $mockHttpClient->method('request')
            ->will($this->throwException(new InvalidRequestException('Invalid data.', ['errors' => []])));

        // 2. Expect that same exception type to be thrown from our test.
        $this->expectException(InvalidRequestException::class);

        // 3. Call the service method. It should bubble up the exception.
        $paymentService = new PaymentService($this->client, $mockHttpClient);
        $paymentService->retrieve('pay_123');
    }
}
