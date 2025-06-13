<?php

namespace Paymongo\Entities;

use Paymongo\Entities\Billing;

class Source extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?string $type;
    public ?int $amount;
    public ?string $currency;
    public ?string $description;
    public ?bool $livemode;
    public ?string $status;
    public ?Billing $billing;
    public ?int $created_at;
    public ?int $updated_at;

    /** @var object|null - Contains URLs for redirection. */
    public ?Redirect $redirect;

    /** @var object|array|null */
    public $metadata;

    /**
     * @param object $apiResource The raw source object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->type = $attributes['type'] ?? null;
        $this->amount = $attributes['amount'] ?? null;
        $this->currency = $attributes['currency'] ?? null;
        $this->description = $attributes['description'] ?? null;
        $this->livemode = $attributes['livemode'] ?? null;
        $this->status = $attributes['status'] ?? null;
        $this->metadata = $attributes['metadata'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;

        // Safely instantiate the nested Billing object.
        $billingData = $attributes['billing'] ?? null;
        $this->billing = is_array($billingData) ? new Billing($billingData) : null;

        $redirectData = $attributes['redirect'] ?? null;
        $this->redirect = is_array($redirectData) ? new Redirect($redirectData) : null;
    }
}
