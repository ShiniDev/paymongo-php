<?php

namespace Paymongo\Entities;

use Paymongo\Entities\Billing;

class PaymentMethod extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?string $type;
    public ?Billing $billing;

    /** @var object|array|null - Contains details specific to the payment method type (e.g., card number, expiry). */
    public $details;

    /** @var object|array|null */
    public $metadata;

    public ?int $created_at;
    public ?int $updated_at;

    /**
     * @param object $apiResource The raw PaymentMethod object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->type = $attributes['type'] ?? null;
        $this->details = $attributes['details'] ?? null;
        $this->metadata = $attributes['metadata'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;

        // Safely instantiate the nested Billing object.
        $billingData = $attributes['billing'] ?? null;
        $this->billing = is_array($billingData) ? new Billing($billingData) : null;
    }
}
