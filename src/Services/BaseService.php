<?php

namespace Paymongo\Services;

use Paymongo\HttpClient;
use Paymongo\PaymongoClient;

class BaseService
{
    protected PaymongoClient $client;
    protected HttpClient $httpClient;

    // We add an optional $httpClient parameter.
    public function __construct(PaymongoClient $client, HttpClient $httpClient = null)
    {
        $this->client = $client;

        // If an httpClient is provided (during a test), use it.
        // Otherwise, create a new real one.
        $this->httpClient = $httpClient ?? new HttpClient($this->client->getApiKey());
    }
}
