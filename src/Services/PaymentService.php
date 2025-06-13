<?php

namespace Paymongo\Services;

use Paymongo\ApiResource;
use Paymongo\Entities\Listing;
use Paymongo\Entities\Payment;
use Paymongo\Exceptions\ApiException;

class PaymentService extends BaseService
{
    const URI = '/payments';

    /**
     * Creates a new Payment.
     * @param array $params The data for the new payment.
     * @return Payment The created Payment object.
     * @throws ApiException
     */
    public function create(array $params): Payment
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        return new Payment($apiResource);
    }

    /**
     * Retrieves a list of all Payments.
     * @param array $params Optional query parameters for the list.
     * @return Listing A Listing object containing the array of Payments.
     * @throws ApiException
     */
    public function all(array $params = []): Listing
    {
        $apiResponse = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        // Use array_map for a cleaner transformation of the list data.
        // The logic of wrapping each item in an ApiResource is preserved from the original code.
        $payments = array_map(
            fn($row) => new Payment(new ApiResource($row)),
            $apiResponse->data
        );

        return new Listing([
            'has_more' => $apiResponse->hasMore ?? false,
            'data'     => $payments,
        ]);
    }

    /**
     * Retrieves a specific Payment by its ID.
     * @param string $id The ID of the Payment to retrieve.
     * @return Payment The retrieved Payment object.
     * @throws ApiException
     */
    public function retrieve(string $id): Payment
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new Payment($apiResource);
    }

    /**
     * Helper method to build the full API endpoint URL.
     *
     * @param string $id Optional resource ID.
     * @return string The complete URL.
     */
    private function buildUrl(string $id = ''): string
    {
        $url = "{$this->client->apiBaseUrl}/{$this->client->apiVersion}" . self::URI;

        if ($id !== '') {
            $url .= "/{$id}";
        }

        return $url;
    }
}
