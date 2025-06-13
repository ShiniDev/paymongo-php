<?php

namespace Paymongo;

use Paymongo\Services\BaseService;
use Paymongo\Services\ServiceFactory;
use Paymongo\Services\WebhookService;

/**
 * The main client class for interacting with the PayMongo API.
 *
 * @property-read \Paymongo\Services\CustomerService $customers
 * @property-read \Paymongo\Services\LinkService $links
 * @property-read \Paymongo\Services\PaymentIntentService $paymentIntents
 * @property-read \Paymongo\Services\PaymentMethodService $paymentMethods
 * @property-read \Paymongo\Services\PaymentService $payments
 * @property-read \Paymongo\Services\RefundService $refunds
 * @property-read \Paymongo\Services\SourceService $sources
 * @property-read \Paymongo\Services\WebhookService $webhooks
 */
class PaymongoClient
{
    public string $apiBaseUrl = 'https://api.paymongo.com';
    public string $apiVersion = 'v1';

    private string $apiKey;
    private ServiceFactory $serviceFactory;

    private int $webhookSignatureTolerance;

    /**
     * @var array<string, BaseService> Caches instantiated services for performance.
     */
    private array $services = [];

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->serviceFactory = new ServiceFactory();

        $this->webhookSignatureTolerance = WebhookService::DEFAULT_WEBHOOK_TOLERANCE;
    }

    /**
     * A magic method to lazily load and cache API service objects.
     *
     * @param string $name The name of the service to retrieve.
     * @return BaseService The service object.
     */
    public function __get(string $name): BaseService
    {
        // 1. Check if the service is already instantiated in our cache.
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        // 2. If not, get the service's class name from the factory.
        $serviceClass = $this->serviceFactory->get($name);

        // 3. Instantiate the service, passing this client instance to it.
        $serviceInstance = new $serviceClass($this);

        // 4. Store the new instance in the cache for next time.
        $this->services[$name] = $serviceInstance;

        return $serviceInstance;
    }

    /**
     * Public getter for the API key, used by services.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Sets a custom tolerance in seconds for webhook signature verification.
     *
     * @param int $tolerance The time difference in seconds to allow.
     */
    public function setWebhookSignatureTolerance(int $tolerance): void
    {
        $this->webhookSignatureTolerance = $tolerance;
    }

    /**
     * Gets the currently configured webhook signature tolerance.
     */
    public function getWebhookSignatureTolerance(): int
    {
        return $this->webhookSignatureTolerance;
    }
}
