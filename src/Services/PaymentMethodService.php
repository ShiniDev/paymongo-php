<?php

namespace Paymongo\Services;

use Paymongo\Entities\PaymentMethod;
use Paymongo\Exceptions\ApiException;

class PaymentMethodService extends \Paymongo\Services\BaseService
{
    const URI = '/payment_methods';

    /**
     * Retrieves a specific Payment Method by its ID.
     * @param string $id The ID of the Payment Method to retrieve.
     * @return PaymentMethod The retrieved PaymentMethod object.
     * @throws ApiException
     */
    public function retrieve(string $id): PaymentMethod
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new PaymentMethod($apiResource);
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
