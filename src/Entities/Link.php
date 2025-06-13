<?php

namespace Paymongo\Entities;

// Import child entities for cleaner type declarations and instantiation.
use Paymongo\Entities\Payment;

class Link extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?int $amount;
    public ?bool $archived;
    public ?string $currency;
    public ?string $description;
    public ?bool $livemode;
    public ?int $fee;
    public ?string $remarks;
    public ?string $status;
    public ?int $tax_amount;
    public ?string $checkout_url;
    public ?string $reference_number;

    /** @var Payment[]|null */
    public ?array $payments;

    /** @var array|null */
    public ?array $taxes;

    /** @var object|array|null */
    public $metadata; // Left untyped for flexibility, as metadata can be any shape.

    public ?int $created_at;
    public ?int $updated_at;

    /**
     * @param object $apiResource The raw link object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->amount = $attributes['amount'] ?? null;
        $this->archived = $attributes['archived'] ?? null;
        $this->currency = $attributes['currency'] ?? null;
        $this->description = $attributes['description'] ?? null;
        $this->livemode = $attributes['livemode'] ?? null;
        $this->fee = $attributes['fee'] ?? null;
        $this->remarks = $attributes['remarks'] ?? null;
        $this->status = $attributes['status'] ?? null;
        $this->tax_amount = $attributes['tax_amount'] ?? null;
        $this->checkout_url = $attributes['checkout_url'] ?? null;
        $this->reference_number = $attributes['reference_number'] ?? null;
        $this->taxes = $attributes['taxes'] ?? null;
        $this->metadata = $attributes['metadata'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;

        $this->payments = null;
        if (!empty($attributes['payments'])) {
            $this->payments = [];
            foreach ($attributes['payments'] as $paymentData) {
                $this->payments[] = new Payment($paymentData);
            }
        }
    }
}
