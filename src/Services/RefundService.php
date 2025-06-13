<?php

namespace Paymongo\Services;

use Paymongo\ApiResource;
use Paymongo\Entities\Listing;
use Paymongo\Entities\Refund;
use Paymongo\Exceptions\ApiException;

class RefundService extends BaseService
{
    const URI = '/refunds';

    /**
     * Creates a new Refund.
     * @param array $params The data for the new refund.
     * @return Refund The created Refund object.
     * @throws ApiException
     */
    public function create(array $params): Refund
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        return new Refund($apiResource);
    }

    /**
     * Retrieves a list of all Refunds.
     * @param array $params Optional query parameters for the list.
     * @return Listing A Listing object containing the array of Refunds.
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
        $refunds = array_map(
            fn($row) => new Refund(new ApiResource($row)),
            $apiResponse->data
        );

        return new Listing([
            'has_more' => $apiResponse->hasMore ?? false,
            'data'     => $refunds,
        ]);
    }

    /**
     * Retrieves a specific Refund by its ID.
     * @param string $id The ID of the Refund to retrieve.
     * @return Refund The retrieved Refund object.
     * @throws ApiException
     */
    public function retrieve(string $id): Refund
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new Refund($apiResource);
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
