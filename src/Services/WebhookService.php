<?php

namespace Paymongo\Services;

use Paymongo\ApiResource;
use Paymongo\Entities\Event;
use Paymongo\Entities\Listing;
use Paymongo\Entities\Webhook;
use Paymongo\Exceptions\ApiException;
use Paymongo\Exceptions\SignatureVerificationException;

class WebhookService extends \Paymongo\Services\BaseService
{
    const URI = '/webhooks';

    // A 5-minute tolerance for webhook timestamps to prevent replay attacks.
    public const DEFAULT_WEBHOOK_TOLERANCE = 300;

    /**
     * Creates a new Webhook.
     * @param array $params The data for the new webhook.
     * @return Webhook The created Webhook object.
     * @throws ApiException
     */
    public function create(array $params): Webhook
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        return new Webhook($apiResource);
    }

    /**
     * Retrieves a list of all Webhooks.
     * @return Listing A Listing object containing the array of Webhooks.
     * @throws ApiException
     */
    public function all(): Listing
    {
        $apiResponse = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl(),
        ]);

        $webhooks = array_map(
            fn($row) => new Webhook(new ApiResource($row)),
            $apiResponse->data
        );

        return new Listing([
            'has_more' => $apiResponse->hasMore, // Corrected from has_more
            'data'     => $webhooks,
        ]);
    }

    /**
     * Retrieves a specific Webhook by its ID.
     * @param string $id The ID of the Webhook to retrieve.
     * @return Webhook The retrieved Webhook object.
     * @throws ApiException
     */
    public function retrieve(string $id): Webhook
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new Webhook($apiResource);
    }

    /**
     * Updates a specific Webhook.
     * @param string $id The ID of the Webhook to update.
     * @param array $params The data to update.
     * @return Webhook The updated Webhook object.
     * @throws ApiException
     */
    public function update(string $id, array $params): Webhook
    {
        $apiResource = $this->httpClient->request([
            'method' => 'PUT',
            'url'    => $this->buildUrl($id),
            'params' => $params
        ]);

        return new Webhook($apiResource);
    }

    /**
     * Disables a specific Webhook.
     * @param string $id The ID of the Webhook to disable.
     * @return Webhook The disabled Webhook object.
     * @throws ApiException
     */
    public function disable(string $id): Webhook
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'disable'),
        ]);

        return new Webhook($apiResource);
    }

    /**
     * Enables a specific Webhook.
     * @param string $id The ID of the Webhook to enable.
     * @return Webhook The enabled Webhook object.
     * @throws ApiException
     */
    public function enable(string $id): Webhook
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'enable'),
        ]);

        return new Webhook($apiResource);
    }

    /**
     * Validates and constructs an Event object from a webhook payload.
     *
     * @param string $payload The raw JSON payload from the request body.
     * @param string $signatureHeader The value of the 'Paymongo-Signature' HTTP header.
     * @param string $webhookSecretKey The webhook's secret key from your dashboard.
     * @param int $tolerance The allowed time difference in seconds to prevent replay attacks.
     * @return Event The verified Event object.
     * @throws SignatureVerificationException if the signature is invalid or the timestamp is out of bounds.
     */
    public function constructEvent(
        string $payload,
        string $signatureHeader,
        string $webhookSecretKey,
        int $tolerance = null
    ): Event {
        $tolerance = $tolerance ?? $this->client->getWebhookSignatureTolerance();
        // 1. Parse the header to extract timestamp and signatures
        $timestamp = null;
        $liveSignature = null;
        $testSignature = null;

        foreach (explode(',', $signatureHeader) as $part) {
            $pair = explode('=', $part, 2);
            if (count($pair) !== 2) {
                continue;
            }
            match ($pair[0]) {
                't' => $timestamp = (int)$pair[1],
                'live' => $liveSignature = $pair[1],
                'test' => $testSignature = $pair[1],
                default => null,
            };
        }

        if (!$timestamp || (!$liveSignature && !$testSignature)) {
            throw new SignatureVerificationException('Unable to extract timestamp or signature from header.');
        }

        // 2. Protect against replay attacks by checking if the timestamp is within tolerance
        if ($tolerance > 0 && abs(time() - $timestamp) > $tolerance) {
            throw new SignatureVerificationException('Webhook timestamp exceeds tolerance.');
        }

        // 3. Recreate the signature using the timestamp, payload, and secret key
        $expectedSignature = hash_hmac('sha256', "{$timestamp}.{$payload}", $webhookSecretKey);

        // 4. Compare the expected signature with the one from the header
        $actualSignature = $liveSignature ?? $testSignature;

        // CRITICAL: Use hash_equals for a timing-attack-safe comparison
        if (!hash_equals($expectedSignature, $actualSignature)) {
            throw new SignatureVerificationException('Webhook signature does not match expected signature.');
        }

        // 5. If successful, construct and return the Event object
        $decodedPayload = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SignatureVerificationException('Invalid JSON payload provided.');
        }

        $apiResource = new ApiResource($decodedPayload);
        return new Event($apiResource);
    }

    /**
     * Helper method to build the full API endpoint URL.
     */
    private function buildUrl(string $id = '', string $action = ''): string
    {
        $url = "{$this->client->apiBaseUrl}/{$this->client->apiVersion}" . self::URI;
        if ($id !== '') {
            $url .= "/{$id}";
        }
        if ($action !== '') {
            $url .= "/{$action}";
        }
        return $url;
    }
}
