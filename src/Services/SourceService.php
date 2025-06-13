<?php

namespace Paymongo\Services;

use Paymongo\Entities\Source;
use Paymongo\Exceptions\ApiException;

class SourceService extends \Paymongo\Services\BaseService
{
    const URI = '/sources';

    /**
     * Creates a new Source.
     * @param array $params The data for the new source.
     * @return Source The created Source object.
     * @throws ApiException
     */
    public function create(array $params): Source
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        return new Source($apiResource);
    }

    /**
     * Retrieves a specific Source by its ID.
     * @param string $id The ID of the Source to retrieve.
     * @return Source The retrieved Source object.
     * @throws ApiException
     */
    public function retrieve(string $id): Source
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new Source($apiResource);
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
