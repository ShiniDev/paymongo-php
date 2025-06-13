<?php

namespace Paymongo\Services;

use Paymongo\ApiResource;
use Paymongo\Entities\Link;
use Paymongo\Entities\Listing;
use Paymongo\Exceptions\ApiException;

class LinkService extends BaseService
{
    const URI = '/links';

    /**
     * Creates a new Payment Link.
     * @param array $params The data for the new link.
     * @return Link The created Link object.
     * @throws ApiException
     */
    public function create(array $params): Link
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        return new Link($apiResource);
    }

    /**
     * Retrieves a list of all Payment Links.
     * @param array $params Optional query parameters for the list.
     * @return Listing A Listing object containing the array of Links.
     * @throws ApiException
     */
    public function all(array $params = []): Listing
    {
        $apiResponse = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl(),
            'params' => $params
        ]);

        // Use array_map for a cleaner, more functional approach to transforming the data.
        $links = array_map(
            fn($row) => new Link(new ApiResource($row)),
            $apiResponse->data
        );

        return new Listing([
            'has_more' => $apiResponse->hasMore ?? false,
            'data'     => $links,
        ]);
    }

    /**
     * Retrieves a specific Payment Link by its ID.
     * @param string $id The ID of the Link to retrieve.
     * @return Link The retrieved Link object.
     * @throws ApiException
     */
    public function retrieve(string $id): Link
    {
        $apiResource = $this->httpClient->request([
            'method' => 'GET',
            'url'    => $this->buildUrl($id),
        ]);

        return new Link($apiResource);
    }

    /**
     * Archives a specific Payment Link.
     * @param string $id The ID of the Link to archive.
     * @return Link The archived Link object.
     * @throws ApiException
     */
    public function archive(string $id): Link
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'archive'),
        ]);

        return new Link($apiResource);
    }

    /**
     * Unarchives a specific Payment Link.
     * @param string $id The ID of the Link to unarchive.
     * @return Link The unarchived Link object.
     * @throws ApiException
     */
    public function unarchive(string $id): Link
    {
        $apiResource = $this->httpClient->request([
            'method' => 'POST',
            'url'    => $this->buildUrl($id, 'unarchive'),
        ]);

        return new Link($apiResource);
    }

    /**
     * Helper method to build the full API endpoint URL, now with support for actions.
     *
     * @param string $id Optional resource ID.
     * @param string $action Optional action name (e.g., 'archive').
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
