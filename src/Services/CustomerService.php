<?php

namespace Paymongo\Services;

use Paymongo\Entities\Customer;
use Paymongo\Exceptions\ApiException;

class CustomerService extends BaseService
{
    const URI = '/customers';

    /**
     * Creates a new Customer.
     * @param array $params The data for the new customer.
     * @return Customer The created Customer object.
     * @throws ApiException
     */
    public function create(array $params): Customer
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);
        return new Customer($apiResource);
    }

    /**
     * Retrieves a specific Customer by their ID.
     * @param string $id The ID of the Customer to retrieve.
     * @return Customer The retrieved Customer object.
     * @throws ApiException
     */
    public function retrieve(string $id): Customer
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new Customer($apiResource);
    }

    /**
     * Updates a specific Customer.
     * @param string $id The ID of the Customer to update.
     * @param array $params The data to update.
     * @return Customer The updated Customer object.
     * @throws ApiException
     */
    public function update(string $id, array $params): Customer
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id),
            'params' => $params
        ]);

        return new Customer($apiResource);
    }

    /**
     * Deletes a specific Customer.
     * @param string $id The ID of the Customer to delete.
     * @return Customer The deleted Customer object.
     * @throws ApiException
     */
    public function delete(string $id): Customer
    {
        $apiResource = $this->httpClient->request([
            'method' => 'DELETE',
            'url'    => $this->buildUrl($id),
        ]);

        return new Customer($apiResource);
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
