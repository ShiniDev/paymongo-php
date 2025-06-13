<?php

namespace Paymongo\Services;

use Paymongo\Exceptions\InvalidServiceException;

// Import all service classes for a cleaner mapping array.
use Paymongo\Services\CustomerService;
use Paymongo\Services\LinkService;
use Paymongo\Services\PaymentIntentService;
use Paymongo\Services\PaymentMethodService;
use Paymongo\Services\PaymentService;
use Paymongo\Services\RefundService;
use Paymongo\Services\SourceService;
use Paymongo\Services\WebhookService;

/**
 * A factory class that provides the class names for API services.
 */
class ServiceFactory
{
    /**
     * @var array<string, string> A map of service names to their fully-qualified class names.
     */
    private array $classes = [
        'customers'      => CustomerService::class,
        'links'          => LinkService::class,
        'payments'       => PaymentService::class,
        'paymentIntents' => PaymentIntentService::class,
        'paymentMethods' => PaymentMethodService::class,
        'refunds'        => RefundService::class,
        'sources'        => SourceService::class,
        'webhooks'       => WebhookService::class,
    ];

    /**
     * Retrieves the fully-qualified class name for a given service.
     *
     * @param string $name The short name of the service (e.g., 'links').
     * @return string The class name (e.g., 'Paymongo\Services\LinkService').
     * @throws InvalidServiceException if the service name is not found.
     */
    public function get(string $name): string
    {
        if (\array_key_exists($name, $this->classes)) {
            return $this->classes[$name];
        }

        throw new InvalidServiceException("Service '{$name}' does not exist.");
    }
}
