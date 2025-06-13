<?php

namespace Paymongo\Entities;

// Import related entities for cleaner code.
use Paymongo\ApiResource;
use Paymongo\Entities\Billing;
use Paymongo\Entities\Refund;

class Payment extends BaseEntity
{
    public ?string $id;
    public ?int $amount;
    public ?Billing $billing;
    public ?string $currency;
    public ?string $description;
    public ?int $fee;
    public ?bool $livemode;
    public ?int $net_amount;
    public ?string $statement_descriptor;
    public ?string $status;
    public ?int $available_at;
    public ?int $created_at;
    public ?int $paid_at;
    public ?string $payout;
    public ?int $updated_at;
    public ?int $tax_amount;
    public ?string $payment_intent_id;

    /** @var object|array|null */
    public $metadata;

    /** @var object|null - The source of the payment (e.g., card, gcash). */
    public ?array $source;

    /** @var Refund[]|null */
    public ?array $refunds;

    /** @var array|null */
    public ?array $taxes;

    /**
     * @param object $apiResource The raw payment object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->amount = $attributes['amount'] ?? null;
        $this->currency = $attributes['currency'] ?? null;
        $this->description = $attributes['description'] ?? null;
        $this->fee = $attributes['fee'] ?? null;
        $this->livemode = $attributes['livemode'] ?? null;
        $this->net_amount = $attributes['net_amount'] ?? null;
        $this->statement_descriptor = $attributes['statement_descriptor'] ?? null;
        $this->status = $attributes['status'] ?? null;
        $this->available_at = $attributes['available_at'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->paid_at = $attributes['paid_at'] ?? null;
        $this->payout = $attributes['payout'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;
        $this->metadata = $attributes['metadata'] ?? null;
        $this->source = $attributes['source'] ?? null;
        $this->tax_amount = $attributes['tax_amount'] ?? null;
        $this->payment_intent_id = $attributes['payment_intent_id'] ?? null;
        $this->taxes = $attributes['taxes'] ?? null;

        // Safely instantiate the nested Billing object.
        $billingData = $attributes['billing'] ?? null;
        $this->billing = is_array($billingData) ? new Billing($billingData) : null;

        $sourceData = $attributes['source'] ?? null;
        $this->source = is_null($sourceData) ? null : (array) $sourceData;

        // Safely instantiate nested Refund objects.
        $this->refunds = [];
        if (!empty($attributes['refunds']) && is_array($attributes['refunds'])) {
            foreach ($attributes['refunds'] as $refund) {
                // Preserving original logic of wrapping refund data in ApiResource.
                $rowApiResource = new ApiResource($refund);
                $this->refunds[] = new Refund($rowApiResource);
            }
        }
    }
}
