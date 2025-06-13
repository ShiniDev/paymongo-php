<?php

namespace Paymongo\Services;

use Paymongo\Entities\PaymentIntent;
use Paymongo\Exceptions\ApiException;

class PaymentIntentService extends \Paymongo\Services\BaseService
{
    const URI = '/payment_intents';

    /**
     * Creates a new Payment Intent.
     * @param array $params The data for the new payment intent.
     * @return PaymentIntent The created PaymentIntent object.
     * @throws ApiException
     */
    public function create(array $params): PaymentIntent
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params // CORRECT: Pass raw params. HttpClient will wrap it.
        ]);

        return new PaymentIntent($apiResource);
    }

    /**
     * Retrieves a specific Payment Intent by its ID.
     * @param string $id The ID of the Payment Intent to retrieve.
     * @return PaymentIntent The retrieved PaymentIntent object.
     * @throws ApiException
     */
    public function retrieve(string $id): PaymentIntent
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new PaymentIntent($apiResource);
    }

    /**
     * Attaches a Payment Method to a Payment Intent.
     * @param string $id The ID of the Payment Intent.
     * @param array $params The attach data, typically including a `payment_method` ID.
     * @return PaymentIntent The updated PaymentIntent object.
     * @throws ApiException
     */
    public function attach(string $id, array $params): PaymentIntent
    {
        // This logic was in the original code and is preserved.
        $params['origin'] = 'php';

        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'attach'),
            'params' => $params // CORRECT: Pass raw params.
        ]);

        return new PaymentIntent($apiResource);
    }

    /**
     * Captures a specific Payment Intent.
     * @param string $id The ID of the Payment Intent to capture.
     * @param array $params Optional parameters for the capture.
     * @return PaymentIntent The captured PaymentIntent object.
     * @throws ApiException
     */
    public function capture(string $id, array $params = []): PaymentIntent
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'capture'),
            'params' => $params // CORRECT: Pass raw params.
        ]);

        return new PaymentIntent($apiResource);
    }

    /**
     * Cancels a specific Payment Intent.
     * @param string $id The ID of the Payment Intent to cancel.
     * @return PaymentIntent The cancelled PaymentIntent object.
     * @throws ApiException
     */
    public function cancel(string $id): PaymentIntent
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'cancel'),
            // No 'params' key since there is no payload.
        ]);

        return new PaymentIntent($apiResource);
    }

    /**
     * Helper method to build the full API endpoint URL, with support for actions.
     *
     * @param string $id Optional resource ID.
     * @param string $action Optional action name (e.g., 'capture').
     * @return string The complete URL.
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
